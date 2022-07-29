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
		//$taxonomy = 'inpursuit-' . $field_name;
		$field_name = apply_filters( 'inpursuit_rest_callback_field', $field_name );
		wp_set_object_terms( $post->ID, $value, $field_name );
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
				/*
		    if( has_post_thumbnail( $id ) ){
		      $img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
		      $url = $img_arr[0];
		      return $url;
		    } else {
		      return plugins_url( "InPursuit/dist/images/default-profile.png" );
		    }
				*/
		  }
		);

	}



}
