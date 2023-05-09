<?php

class INPURSUIT_REST_POST_BASE extends INPURSUIT_REST_BASE{

	private $admin_ui;
	private $post_type;
	private $field_names;

	function __construct(){
		add_filter( 'inpursuit_rest_field', function( $field_name ){
			$fields = $this->getFieldNames();
			if( isset( $fields[ $field_name] ) ) return $fields[ $field_name ];
			return $field_name;
		} );

		add_filter( 'inpursuit_rest_callback_field', function( $field_name ){
			$fields = $this->getFieldNames();
			if( is_array( $fields ) && count( $fields ) ){
				$fields = array_flip( $this->getFieldNames() );
				if( isset( $fields[ $field_name] ) ) return $fields[ $field_name ];
			}
			return $field_name;
		} );

		parent::__construct();
	}

	/* GETTER AND SETTER FUNCTIONS */
	function getFieldNames(){ return $this->field_names; }
	function setFieldNames( $field_names ){ $this->field_names = $field_names; }

	function getAdminUI(){ return $this->admin_ui; }
	function setAdminUI( $admin_ui ){ $this->admin_ui = $admin_ui; }

	function getPostType(){ return $this->post_type; }
	function setPostType( $post_type ){ $this->post_type = $post_type; }
	/* GETTER AND SETTER FUNCTIONS */

	/* REST CALLBACK FUNCTIONS FOR TERMS */
	function getCallbackForTerm( $object, $field_name, $request ){
		//$taxonomy = 'inpursuit-' . $field_name;
		$field_name = apply_filters( 'inpursuit_rest_callback_field', $field_name );
		$terms = wp_get_object_terms( $object['id'], $field_name, array( 'fields' => 'ids' ) );

		$single_taxonomy_list = array( 'inpursuit-gender', 'inpursuit-status', 'inpursuit-location', 'inpursuit-event-type' );

		if( is_array( $terms ) && in_array( $field_name, $single_taxonomy_list ) ){
			if( count( $terms ) ) return $terms[0];
			else return '';
		}
		return $terms;
	}

	function updateCallbackForTerm( $value, $post, $field_name, $request, $object_type ){

		$taxonomy = apply_filters( 'inpursuit_rest_callback_field', $field_name );


		/*
		* IF CHANGE IN STATUS
		* ADD AS COMMENT FOR NOTIFICATION
		*/

		$field_labels = array(
			'member_status' => 'Status',
			'group'					=> 'Group'
		);


		if( isset( $field_labels[ $field_name ] ) && $field_labels[ $field_name ] ){

			$field_label = $field_labels[ $field_name ];

			$old_terms = wp_get_object_terms( $post->ID, $taxonomy, array(
				'fields' => 'ids'
			) );

			// CHECK FOR VALUE IF CHANGED
			// ADD TO COMMENT SECTION
			if( is_array( $old_terms ) && count( $old_terms ) && ( $old_terms[0] != $value ) ){

				$old_term = get_term_by( 'term_taxonomy_id', $old_terms[0] );
				$old_term_name = $old_term->name;

				$new_term = get_term_by( 'term_taxonomy_id', intval( $value ) );
				$new_term_name = $new_term->name;

				$comment_db = INPURSUIT_DB_COMMENT::getInstance();
				$item = $comment_db->sanitize( array(
					'comment'	=> "$field_label changed from $old_term_name to $new_term_name",
					'post'		=> $post->ID
				) );
				$comment_db->insert( $item );
			}
		}

		// SET NEW TERM FIELDS
		wp_set_object_terms( $post->ID, $value, $taxonomy );


	}
	/* REST CALLBACK FUNCTIONS FOR TERMS */

	/* REST CALLBACK FUNCTIONS FOR META */
	function getCallbackForMeta( $object,  $field_name, $request ){
    return get_post_meta( $object['id'], $field_name, array( 'fields' => 'names' ) );
  }

  function updateCallbackForMeta( $value, $post, $field_name, $request, $object_type ){
    update_post_meta( $post->ID, $field_name, $value );
  }
	/* REST CALLBACK FUNCTIONS FOR META */

	function addRestData(){
		$admin_ui = $this->getAdminUI();
		$post_type = $this->getPostType();

		// ADD TAXONOMY TERMS TO THE REST API
		$taxonomies = apply_filters( "inpursuit-rest-taxonomies-$post_type", $admin_ui->getTaxonomiesForDropdown() );
		if( is_array( $taxonomies ) && count( $taxonomies ) ){
			foreach( $taxonomies as $taxonomy_slug => $taxonomy_label ){
				//$field_name = str_replace( 'inpursuit-', '', $taxonomy_slug ) ;
				$this->registerRestField( $taxonomy_slug, array( $this, 'getCallbackForTerm' ), array( $this, 'updateCallbackForTerm' ) );
			}
		}


		// ADD META FIELDS TO THE REST API
		$metafields = $admin_ui->getMetaFields();
		if( is_array( $metafields ) && count( $metafields ) ){
			foreach( $metafields as $meta_slug => $meta_title ){
				$this->registerRestField( $meta_slug, array( $this, 'getCallbackForMeta' ), array( $this, 'updateCallbackForMeta' ) );
			}
		}

		// ADMIN URL TO EDIT THE POST
		$this->registerRestField(
			'edit_url',
			function( $post, $field_name, $request ){
				return admin_url( 'post.php?action=edit&post=' . $post['id'] );
			}
		);

		// AUTHOR NAME
		$this->registerRestField(
			'author_name',
			function( $post, $field_name, $request ){
				return get_the_author_meta( 'display_name', $post['author'] );
			}
		);

		// FEATURED IMAGE
		$this->registerRestField(
		  'featured_image',
		  function( $post, $field_name, $request ){
		    $id = $post['id'];
				return INPURSUIT_DB::getInstance()->getFeaturedImageURL( $id );
			}
		);

	}

	function prepareItemResponse( $post_id ){
		$post = get_post( $post_id );
		return array(
			'id'              => $post->ID,
			'title'           => array(
				'rendered' => $post->post_title
			),
			'featured_image'  => INPURSUIT_DB::getInstance()->getFeaturedImageURL( $post_id ),
			'slug'            => $post->post_name,
			'type'            => $post->post_type,
			'author'					=> intval( $post->post_author ),
			'date'						=> $post->post_date,
			'date_gmt'				=> $post->post_date_gmt,
			'modified'				=> $post->post_modified,
			'modified_gmt'		=> $post->post_modified_gmt,
			'status'					=> $post->post_status,
			//'data'            => $post
		);
	}



}
