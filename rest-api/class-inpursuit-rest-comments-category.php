<?php

class INPURSUIT_REST_COMMENTS_CATEGORY extends WP_REST_Controller {

	/**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'inpursuit/v' . $version;
    $base = 'comments-category';

		register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_items' ),
        'permission_callback' => 'is_user_logged_in',
        'args'                => array(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args'                => array(
          'name' =>  array(
            'description'   => 'Comment category name',
            'type'          => 'String',
            'required'      => true
          )
        )
      )
    ) );

    register_rest_route( $namespace, '/' . $base . '/(?P<term_id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_item' ),
        'permission_callback' => 'is_user_logged_in'
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array( $this, 'update_item' ),
        'permission_callback' => array( $this, 'update_item_permissions_check' ),
        'args'                => array(
          'name' =>  array(
            'description'   => 'Comment category name',
            'type'          => 'String',
            'required'      => true
          )
        )
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array( $this, 'delete_item' ),
        'permission_callback' => array( $this, 'delete_item_permissions_check' )
      )
    ) );

  }

  public function get_items( $request ) {
    $data                 = array();
    $params               = $request->get_params();
		$comments_category_db = INPURSUIT_DB_COMMENTS_CATEGORY::getInstance();
    $response_data        = $comments_category_db->getResults( $params );

    foreach( $response_data['data'] as $row ){
      $item = $this->prepare_item_for_response( $row, $request );
      array_push( $data, $item );
    }

		$response = new WP_REST_Response( $data, 200 );
		$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
		$response->header( 'X-WP-Total', $response_data['total'] );
		return $response;
	}

	public function get_item( $request ){
    $term_id = $request['term_id'];
    $error   = new WP_Error( 'rest_term_invalid', __( 'Term does not exist.' ), array( 'status' => 404 ) );

    if ( (int) $term_id <= 0 ) {
			return $error;
		}

    $comments_category_db = INPURSUIT_DB_COMMENTS_CATEGORY::getInstance();
		$item                 = $comments_category_db->get_row( $term_id );

    if( !$item ){
      return $error;
    }

		$data  = $this->prepare_item_for_response( $item, $request );
		$response = new WP_REST_Response( $data, 200 );
		$response->header( 'X-WP-TotalPages', 1 );
		$response->header( 'X-WP-Total', 1 );
		return $response;
	}

  /**
   * Creates a single term in comments category.
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item( $request ) {

    if ( '' === trim( $request['name'] ) ) {
      return new WP_Error( 'empty_term_name', __( 'A name is required for this term.' ) );
    }

    $item                   = $this->prepare_item_for_database( $request );
		$comments_category_db   = INPURSUIT_DB_COMMENTS_CATEGORY::getInstance();

    if( $comments_category_db->comment_category_name_exists( $request['name'] ) ){
      return new WP_Error( 'term_exists', __( 'A term with the name provided already exists.' ), $request['name'] );
    }

    $term_id = $comments_category_db->insert( $item );

    if( false === $term_id ){
      return new WP_Error( 'db_insert_error', __( 'Could not insert comment category into the database.' ), array( 'status' => 500 ) );
    }

    return new WP_REST_Response( array('term_id' => $term_id, 'name' => $item['name'] ), 200 );

  }

  /**
   * Updates a single term from comments category by term_id.
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function update_item( $request ) {
    global $wpdb;
    $params    = $request->get_params();
    $term_id   = (int) $params['term_id'];
    $term_name = $params['name'];

    if ( '' === trim( $term_name ) ) {
      return new WP_Error( 'empty_term_name', __( 'A name is required for this term.' ) );
    }

    if ( (int) $term_id <= 0 ) {
			return new WP_Error(
  			'rest_term_invalid',
  			__( 'Term does not exist.' ),
  			array( 'status' => 404 )
  		);
		}

		$comments_category_db = INPURSUIT_DB_COMMENTS_CATEGORY::getInstance();
    $table = $comments_category_db->getTable();

    if( $comments_category_db->comment_category_name_exists( $term_name ) ){
      return new WP_Error( 'term_exists', __( 'A term with the name provided already exists.' ), $term_name );
    }

		$is_updated = $wpdb->update(
      $table,
      array( 'name' => $term_name ),
      array( 'term_id' => $term_id ),
      array()
    );

   if( !$is_updated ){
     return new WP_Error( 'db_update_error', __( 'Could not update comment category in the database.' ), $wpdb->last_error );
   }

		$data = array(
			'term_id' => $term_id,
      'name'    => $term_name
		);

    return new WP_REST_Response( $data, 200 );

  }

  /**
   * Deletes a single term from comments category.
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function delete_item( $request ) {
    global $wpdb;
    $params  = $request->get_params();
    $term_id = (int) $params['term_id'];

    if ( $term_id <= 0 ) {
			return new WP_Error(
  			'rest_term_invalid',
  			__( 'Term does not exist.' ),
  			array( 'status' => 404 )
  		);
		}

    // DELETE COMMENT CATEGORY RELATION DATA BEFORE THE ACTUAL COMMENT CATEGORY IS DELETED
    do_action( "inpursuit_before_delete_comments_category", $term_id );

		$comments_category_db = INPURSUIT_DB_COMMENTS_CATEGORY::getInstance();
    $table                = $comments_category_db->getTable();
		$sql                  = "DELETE FROM $table WHERE term_id = %d;";
		$is_deleted           =  $comments_category_db->query( $comments_category_db->prepare( $sql, $term_id ) );

    if( !$is_deleted ){
      return new WP_Error(
        'rest_cannot_delete',
        __( 'The comment category cannot be deleted.' ),
        array( 'status' => 500 )
      );
    }

    return new WP_REST_Response( true, 200 );

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
    return INPURSUIT_DB_COMMENTS_CATEGORY::getInstance()->sanitize( $request );
  }

  function prepare_item_for_response( $item, $request ){
    return array(
			'term_id'		=> (int) $item->term_id,
      'name'      => $item->name
		);
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
      )
    );
  }

}

add_action( 'rest_api_init', function(){
	$comments_category_controller = new INPURSUIT_REST_COMMENTS_CATEGORY;
	$comments_category_controller->register_routes();
} );
