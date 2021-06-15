<?php

class INPURSUIT_REST_POST_BASE extends INPURSUIT_BASE{

	private $admin_ui;
	private $post_type;

	function __construct(){
		add_action( 'rest_api_init', array( $this, 'addRestData' ) );
	}

	/* GETTER AND SETTER FUNCTIONS */

	function getAdminUI(){ return $this->admin_ui; }
	function setAdminUI( $admin_ui ){ $this->admin_ui = $admin_ui; }

	function getPostType(){ return $this->post_type; }
	function setPostType( $post_type ){ $this->post_type = $post_type; }
	/* GETTER AND SETTER FUNCTIONS */

	/* REST CALLBACK FUNCTIONS FOR TERMS */
	function getCallbackForTerm( $object, $field_name, $request ){
		$terms = wp_get_object_terms( $object['id'], $field_name, array( 'fields' => 'names' ) );
		if( is_array( $terms ) && count( $terms ) == 1 ){
			return $terms[0];
		}
		return $terms;
	}

	function updateCallbackForTerm( $value, $post, $field_name, $request, $object_type ){
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
		$taxonomies = $admin_ui->getTaxonomiesForDropdown();
		foreach( $taxonomies as $taxonomy_slug => $taxonomy_label ){
			$this->registerRestField( $taxonomy_slug, array( $this, 'getCallbackForTerm' ), array( $this, 'updateCallbackForTerm' ) );
		}

		// ADD META FIELDS TO THE REST API
		$metafields = $admin_ui->getMetaFields();
		foreach( $metafields as $meta_slug => $meta_title ){
			$this->registerRestField( $meta_slug, array( $this, 'getCallbackForMeta' ), array( $this, 'updateCallbackForMeta' ) );
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

	}

	// WRAPPER FUNCTION TO REGISTER REST FIELD
	function registerRestField( $field_name, $get_callback, $update_callback = '__return_false', $schema = null ){
		register_rest_field(
			$this->getPostType(),
			$field_name,
			array(
    		'get_callback'    => $get_callback,
    		'update_callback' => $update_callback,
    		'schema'          => $schema,
     	)
		);
	}

}
