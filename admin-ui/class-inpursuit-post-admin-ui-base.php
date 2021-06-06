<?php

class INPURSUIT_POST_ADMIN_UI_BASE extends INPURSUIT_BASE{

	var $post_type;
	var $meta_boxes;

	function __construct(){

		add_action( 'admin_menu', array( $this, 'removeMetaBoxes' ), 100 );

		add_action( 'post_submitbox_misc_actions', array( $this, 'miscActionsDiv' ) );

		/* ENQUEUE SCRIPTS ON ADMIN DASHBOARD */
		add_action( 'admin_enqueue_scripts', array( $this, 'assets') );

		add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes' ) );

	}

	function getPostType(){ return $this->post_type; }
	function setPostType( $post_type ){ $this->post_type = $post_type; }

	function getMetaBoxes(){ return $this->meta_boxes; }
	function setMetaBoxes( $meta_boxes ){ $this->meta_boxes = $meta_boxes; }

	function assets( $hook ) {
		global $post_type;
		if( $post_type == $this->post_type ){

			wp_enqueue_style( 'inpursuit-admin', plugins_url( 'InPursuit/dist/css/admin-style.css' ), array(), INPURSUIT_VERSION );
			wp_enqueue_script( 'axios', 'https://unpkg.com/axios/dist/axios.min.js', array(), null, true );
			wp_enqueue_script( 'vue', 'https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js', array(), null, true );
  		wp_enqueue_script( 'inpursuit-main', plugins_url( 'InPursuit/dist/js/admin-main.js' ), array( 'axios', 'vue' ), null, true);

			wp_localize_script( 'inpursuit-main', 'inpursuitSettings', array(
    		'root' => esc_url_raw( rest_url() ),
    		'nonce' => wp_create_nonce( 'wp_rest' )
			) );

		}
	}

	function miscActionsDiv( $post ){ }

	function removeMetaBoxes(){ }

	function addMetaBoxes(){

		$metaboxes = $this->getMetaBoxes();

		// REGISTER META BOXES
		if( is_array( $metaboxes ) ){
			foreach( $metaboxes as $meta_box ){
				add_meta_box(
					$meta_box['id'], 														// Unique ID
					$meta_box['title'], 												// Box title
					array( $this, 'metaboxHTML' ),
					$this->getPostType(),
					isset( $meta_box['context'] ) ? $meta_box['context'] : 'normal', 	// Context
					'default',																	// Priority
					$meta_box
				);
			}
		}

	}

	function metaboxHTML( $post, $metabox ){
		include( 'templates/metabox-'.$metabox['id'].'.php' );
	}

}
