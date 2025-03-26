<?php

class INPURSUIT_REST_COMMENTS extends WP_REST_Controller {

	/**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'inpursuit/v' . $version;
    $base = 'comments';

		register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_items' ),
        'permission_callback' => '__return_true',
        'args'                => array(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( true ),
      ),
    ) );

    register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_item' ),
        'permission_callback' => '__return_true',
        'args'                => array(
          'context' => array(
            'default' => 'view',
          ),
        ),
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array( $this, 'update_item' ),
        'permission_callback' => array( $this, 'update_item_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( false ),
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array( $this, 'delete_item' ),
        'permission_callback' => array( $this, 'delete_item_permissions_check' ),
        'args'                => array(
          'force' => array(
            'default' => false,
          ),
        ),
      ),
    ) );
    register_rest_route( $namespace, '/' . $base . '/schema', array(
      'methods'  => WP_REST_Server::READABLE,
      'callback' => array( $this, 'get_public_item_schema' ),
    ) );
  }

  public function get_items( $request ) {

    if( is_user_logged_in() ){
      $data       = array();
      $params     = $request->get_params();
      $comment_db = INPURSUIT_DB_COMMENT::getInstance();
      $categories = isset( $params['comments_category'] ) && $params['comments_category'] ? $params['comments_category'] : "";

      if( !current_user_can( 'administrator' ) ){

        $current_user = wp_get_current_user();

        $current_user_id = $current_user->ID;

        $params[ 'user_id' ] = $current_user_id;

      }

      // FILTER COMMENTS BY CATEGORY_IDS
      if( $categories ){
        $comment_ids = INPURSUIT_DB_COMMENTS_CATEGORY_RELATION::getInstance()->get_comment_ids_by_category_ids( $categories );
        $params['comment_ids'] = $comment_ids ? $comment_ids : array(0);
  		}

  		$response_data = $comment_db->getResults( $params );

  		foreach( $response_data['data'] as $row ){
  			$item = $this->prepare_item_for_response( $row, $request );
  			array_push( $data, $item );
  		}

      $response = new WP_REST_Response( $data, 200 );
  		$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
  		$response->header( 'X-WP-Total', $response_data['total'] );
  		return $response;

    }

    return new WP_Error(
      'rest_forbidden_context',
      __( 'Sorry, you are not allowed to view terms for this object.' ),
      array(
        'status' => rest_authorization_required_code(),
      )
    );

	}

	public function get_item( $request ){
		$comment_db = INPURSUIT_DB_COMMENT::getInstance();
		$row = $comment_db->get_row( $request['id'] );

		$data = $this->prepare_item_for_response( $row, $request );

		$response = new WP_REST_Response( $data, 200 );
		$response->header( 'X-WP-TotalPages', 1 );
		$response->header( 'X-WP-Total', 1 );
		return $response;
	}

	function prepare_item_for_response( $item, $request ){

    $comments_category_relation = INPURSUIT_DB_COMMENTS_CATEGORY_RELATION::getInstance();
    $comments_category = $comments_category_relation->get_comment_categories($item->ID);

    return array(
			'id'				=> $item->ID,
			'comment'		=> isset( $item->text ) ? $item->text : $item->comment,

      'member'    => INPURSUIT_REST_MEMBER::getInstance()->prepareItemResponse( $item->post_id ),

      'user'			=> array(
        'id'    => $item->user_id,
        'name'  => get_userdata( $item->user_id )->display_name
      ),
			'post_date'	=> isset( $item->post_date ) ? get_date_from_gmt( $item->post_date ) : get_date_from_gmt( $item->modified_on ),
      'comments_category' => $comments_category
		);
	}


  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item( $request ) {
    $item = $this->prepare_item_for_database( $request );
		$comment_db = INPURSUIT_DB_COMMENT::getInstance();
		$insert_id = $comment_db->insert( $item );

    if( $insert_id ){
      $comments_category_relation = INPURSUIT_DB_COMMENTS_CATEGORY_RELATION::getInstance();

      do_action( 'inpursuit_insert_comment_category_relation', $insert_id, $request );

      // FETCH ALL THE TERMS FOR THE INSERTED COMMENT
      $item['comment_categories'] = $comments_category_relation->get_comment_categories( $insert_id );

      do_action( 'inpursuit_comment_created', $item );
      return new WP_REST_Response( $item, 200 );
		}
		return new WP_Error( 'cant-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function update_item( $request ) {
    $item = $this->prepare_item_for_database( $request );

		$comment_db = INPURSUIT_DB_COMMENT::getInstance();

		$comment = $request->get_json_params();

		$id = isset( $request['id'] ) ? $request['id'] : 0;

    if ($id <= 0) {
      return new WP_Error( 'rest_ip_comment_invalid_id', 'The provided ID is invalid', array( 'status' => 404 ) );
    }

		$comment_db->update($id, $comment );

		$data = array(
			'item'	=> $comment
		);

		return new WP_REST_Response( $data, 200 );

    if ( function_exists( 'slug_some_function_to_update_item' ) ) {
      $data = slug_some_function_to_update_item( $item );
      if ( is_array( $data ) ) {

      }
    }

    return new WP_Error( 'cant-update', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function delete_item( $request ) {

		$comment_db = INPURSUIT_DB_COMMENT::getInstance();

    // DELETE COMMENT CATEGORY RELATION BEFORE THE ACTUAL COMMENT IS DELETED
    do_action( "inpursuit_before_delete_comment", $request['id'] );

		if( $comment_db->delete_row( $request['id'] ) ){
			return new WP_REST_Response( true, 200 );
		}

    return new WP_Error( 'cant-delete', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_items_permissions_check( $request ) {
		return current_user_can( 'edit_posts' );
  }

  /**
   * Check if a given request has access to get a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_item_permissions_check( $request ) {
    return $this->get_items_permissions_check( $request );
  }

  /**
   * Check if a given request has access to create items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function create_item_permissions_check( $request ) {
    return current_user_can( 'edit_posts' );
  }

  /**
   * Check if a given request has access to update a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function update_item_permissions_check( $request ) {
    return $this->create_item_permissions_check( $request );
  }

  /**
   * Check if a given request has access to delete a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function delete_item_permissions_check( $request ) {
		return $this->create_item_permissions_check( $request );
  }

  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database( $request ) {

    return INPURSUIT_DB_COMMENT::getInstance()->sanitize( $request );

    /*
		$data = array(
			'comment'	=> isset( $request['comment'] ) ? $request['comment'] : '',
			'post_id'	=> isset( $request['post'] ) ? $request['post'] : 0,
			'user_id'	=> get_current_user_id()
		);
		return $data;
    */
  }



  /**
   * Get the query params for collections
   *
   * @return array
   */
  public function get_collection_params() {
    return array(
      'page'     => array(
        'description'       => 'Current page of the collection.',
        'type'              => 'integer',
        'default'           => 1,
        'sanitize_callback' => 'absint',
      ),
      'per_page' => array(
        'description'       => 'Maximum number of items to be returned in result set.',
        'type'              => 'integer',
        'default'           => 10,
        'sanitize_callback' => 'absint',
      ),
      'search'   => array(
        'description'       => 'Limit results to those matching a string.',
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ),
    );
  }

}

add_action( 'rest_api_init', function(){
	$history_controller = new INPURSUIT_REST_COMMENTS;
	$history_controller->register_routes();
} );
