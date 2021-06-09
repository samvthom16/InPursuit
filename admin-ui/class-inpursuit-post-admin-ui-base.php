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
			wp_enqueue_script( 'moment', plugins_url( 'InPursuit/dist/js/moment.js' ), array(), null, true);
  		wp_enqueue_script( 'inpursuit-main', plugins_url( 'InPursuit/dist/js/admin-main.js' ), array( 'axios', 'vue', 'moment' ), null, true);

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

	function formField( $atts ){
		//print_r( $atts );

		if( isset( $atts['type'] ) ){

      // SETTING CLASS TO THE FIELD CONTAINER
      $default_class = "orbit-form-group field-".$atts['type'];
      $atts['class'] = isset( $atts['class'] ) ? $atts['class']." ".$default_class : $default_class;
      if( isset( $atts['required'] ) && $atts['required'] ){
        $atts['class'] .= ' orbit-field-required';
      }

      _e( "<div class='" . $atts['class'] . "'>" );

      // DISPLAY LABEL IF THERE IS ANY
      if( isset( $atts['label'] ) && $atts['label'] ){

        $atts['new_label'] = $atts['label'];

        if( isset( $atts['required'] ) && $atts['required'] ){
          $atts['new_label'] .= " <span>*</span>";
        }

        _e("<label>". $atts['new_label'] ."</label>");
      }

      // CHECK IF FORM VALUE IS NOT SET FOR CHECKBOXES THEN SET DEFAULT VALUE TO ARRAY
      switch( $atts['type'] ){
        case 'bt_dropdown_checkboxes':
        case 'checkbox':
          if( !isset( $atts['value'] ) || !is_array( $atts['value'] ) ){ $atts['value'] = array();}
          break;
      }

      $filter_form_dir = plugin_dir_path(__FILE__) . "form-fields/" . $atts['type'] . ".php";

      /* INCLUDE THE FILTER FORM */
      if( file_exists( $filter_form_dir ) ){ include( $filter_form_dir ); }

      // DISPLAY ANY SUBSEQUENT HELP INFORMATION HERE
      if( isset( $atts['help'] ) && $atts['help'] ){ _e("<p class='help'>".$atts['help']."</p>"); }

      _e("</div>");

    }

	}

	function getDashIcon( $slug ){
		$icons = array(
			'birthday'				 	=> 'dashicons-calendar-alt',
			'wedding'				 		=> 'dashicons-image-filter',
			'inpursuit-status' 	=> 'dashicons-performance',
			'inpursuit-gender' 	=> 'dashicons-admin-users',
			'inpursuit-group'	 	=> 'dashicons-networking'
		);
		if( isset( $icons[ $slug ] ) ) return $icons[ $slug ];
		return 'dashicons-admin-tools';
	}

}