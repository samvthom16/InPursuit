<?php

class INPURSUIT_POST_ADMIN_UI_BASE extends INPURSUIT_BASE{

	var $post_type;
	var $meta_boxes;
	var $taxonomies_dropdown;
	var $metafields;

	function __construct(){

		add_action( 'admin_menu', array( $this, 'removeMetaBoxes' ), 100 );

		add_action( 'post_submitbox_misc_actions', function( $post ){
			$post_type = get_post_type( $post );
			if( $post_type != $this->getPostType() ) return '';
			include( 'templates/misc-actions.php' );
		} );

		/* ENQUEUE SCRIPTS ON ADMIN DASHBOARD */
		add_action( 'admin_enqueue_scripts', array( $this, 'assets') );

		add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes' ) );

		// disable wyswyg for custom post type, using the global $post
		add_filter('user_can_richedit', function( $default ){
  		global $post;
  		if( $post->post_type === $this->getPostType() )  return false;
  		return $default;
		});

		// disable for post types
		add_filter( 'use_block_editor_for_' . $this->getPostType(), '__return_false', 10 );
		add_filter('use_block_editor_for_post_type', function( $is_enabled, $post_type ){
			if( $post_type === $this->getPostType() ) return false; // change book to your post type
			return $is_enabled;
		}, 10, 2);

		add_action( 'save_post', array( $this, 'savePost' ), 10, 3 );

		// REMOVE AUTHOR COLUMN
		add_filter('manage_' . $this->getPostType() . '_posts_columns', function ( $columns ) {
    	unset( $columns['author'] );
			return $columns;
		} );

		if ( is_admin() ){
			add_action( 'restrict_manage_posts', function( $post_type ){

				if( $post_type == $this->getPostType() ){

					$taxonomies = $this->getTaxonomiesForDropdown();

					foreach( $taxonomies as $slug => $title ){

						$terms = get_terms( array(
							'taxonomy' 		=> $slug,
							'hide_empty' 	=> true,
						) );

						if( count( $terms ) ){
							_e( "<select name='$slug'>" );
							_e( "<option value=''>All $title</option>" );
							foreach( $terms as $term ){
								$current_v = isset( $_GET[ $slug ] ) ? $_GET[ $slug ] : '';
								printf(
									'<option value="%s"%s>%s</option>',
									$term->slug,
									$term->slug == $current_v? ' selected="selected"':'',
									$term->name
								);
							}
							_e( "</select>" );
						}
					}
				}
			} );
		}

	}

	function getPostType(){ return $this->post_type; }
	function setPostType( $post_type ){ $this->post_type = $post_type; }

	function getMetaBoxes(){ return $this->meta_boxes; }
	function setMetaBoxes( $meta_boxes ){ $this->meta_boxes = $meta_boxes; }

	function getTaxonomiesForDropdown(){ return $this->taxonomies_dropdown; }
	function setTaxonomiesForDropdown( $taxonomies_dropdown ){ $this->taxonomies_dropdown = $taxonomies_dropdown; }

	function getMetaFields(){ return $this->metafields; }
	function setMetaFields( $metafields ){ $this->metafields = $metafields; }

	function removeMetaBoxes(){
		$taxonomies = $this->getTaxonomiesForDropdown();
		if( is_array( $taxonomies ) && count( $taxonomies ) ){
			foreach ( $taxonomies as $slug => $title ) {
				remove_meta_box( $slug . 'div', $this->getPostType(), 'side' );
			}
		}
		remove_meta_box( 'authordiv', $this->getPostType(), 'normal' );
	}

	function savePost( $post_id, $post, $update ){}

	function assets( $hook ) {

		//print_r( $hook );

		global $post_type;
		//if( $post_type == $this->post_type ){

			// CSS FOR CHOROPLETH MAP
			wp_enqueue_style( 'choropleth', plugins_url( 'InPursuit/dist/css/choropleth.css' ), array(), INPURSUIT_VERSION );

			wp_enqueue_style( 'inpursuit-dashboard', plugins_url( 'InPursuit/dist/css/dashboard.css' ), array(), INPURSUIT_VERSION );

			//wp_enqueue_script( 'axios', 'https://unpkg.com/axios/dist/axios.min.js', array(), null, true );
			//wp_enqueue_script( 'vue', 'https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js', array(), null, true );
			//wp_enqueue_script( 'vue-router', 'https://unpkg.com/vue-router@2.0.0/dist/vue-router.js', array('vue'), null, true );

			wp_enqueue_script( 'vue-related', plugins_url( 'InPursuit/dist/js/vue-related.js' ), array(), null, true );

			//wp_enqueue_script( 'vue-datepicker', 'https://unpkg.com/vue-englishdatepicker@0.1.1/dist/vue-englishdatepicker.min.js', array('vue-related'), null, true );
			wp_enqueue_script( 'vue-datepicker', 'https://unpkg.com/vuejs-datepicker@1.6.2/dist/vuejs-datepicker.min.js', array('vue-related'), null, true );

			// JS FOR CHOROPLETH
			wp_enqueue_script( 'leaflet-csv', plugins_url( 'InPursuit/dist/js/leaflet.geocsv.js' ), array(), INPURSUIT_VERSION , true );
			//wp_enqueue_script( 'choropleth', plugins_url( 'InPursuit/dist/js/choropleth.js' ), array( 'leaflet-csv' ), INPURSUIT_VERSION , true );

			//wp_enqueue_script( 'vue-dropdown', plugins_url( 'InPursuit/dist/js/vue-simple-search-dropdown.min.js' ), array( 'vue-related' ), null, true );
			//wp_enqueue_script( 'moment', plugins_url( 'InPursuit/dist/js/moment.js' ), array(), null, true);

			//wp_enqueue_script( 'inpursuit-api', plugins_url( 'InPursuit/dist/js/api.js' ), array(  'vue-related' ), null, true);
			//wp_enqueue_script( 'vue-mixins', plugins_url( 'InPursuit/dist/js/mixins.js' ), array( 'vue-related' ), null, true );
			//wp_enqueue_script( 'inpursuit-vue', plugins_url( 'InPursuit/dist/js/vue-components.js' ), array( 'vue-related', 'vue-mixins' ), null, true);


			//wp_enqueue_script( 'inpursuit-main', plugins_url( 'InPursuit/dist/js/admin.js' ), array( 'vue-related', 'inpursuit-api', 'inpursuit-vue', 'choropleth' ), null, true);

			wp_enqueue_script( 'inpursuit-app', plugins_url( 'InPursuit/dist/js/app-final.js' ), array( 'vue-related', 'vue-datepicker', 'leaflet-csv' ), null, true);

			wp_localize_script( 'inpursuit-app', 'inpursuitSettings', array(
    		'root' => esc_url_raw( rest_url() ),
    		'nonce' => wp_create_nonce( 'wp_rest' )
			) );

		//}
	}



	function addMetaBoxes(){
		$metaboxes = $this->getMetaBoxes();

		// REGISTER META BOXES
		if( is_array( $metaboxes ) ){
			foreach( $metaboxes as $meta_box ){
				add_meta_box(
					$meta_box['id'], 														// Unique ID
					$meta_box['title'], 												// Box title
					function( $post, $metabox ){ include( 'templates/metabox-'.$metabox['id'].'.php' ); },
					$this->getPostType(),
					isset( $meta_box['context'] ) ? $meta_box['context'] : 'normal', 	// Context
					'default',																	// Priority
					$meta_box
				);
			}
		}
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
			'inpursuit-location'=> 'dashicons-admin-site',
			'inpursuit-status' 	=> 'dashicons-performance',
			'inpursuit-gender' 	=> 'dashicons-admin-users',
			'inpursuit-group'	 	=> 'dashicons-networking'
		);
		if( isset( $icons[ $slug ] ) ) return $icons[ $slug ];
		return 'dashicons-admin-tools';
	}

}
