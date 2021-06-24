<?php

	class INPURSUIT_ADMIN_UI extends INPURSUIT_BASE{

		function __construct(){
			add_action( 'wp_dashboard_setup', array( $this, 'setupDashboard' ), 9999 );

			/* ENQUEUE SCRIPTS ON ADMIN DASHBOARD */
			add_action( 'admin_enqueue_scripts', array( $this, 'assets') );

			add_action( 'wp_ajax_sp_combine_map_jsons', array( $this, 'combineMapJsons' ) );
    	add_action( 'wp_ajax_nopriv_sp_combine_map_jsons', array( $this, 'combineMapJsons' ) );

			add_action( 'admin_menu', array( $this, 'adminMenu' ) );

		}

		function adminMenu(){
			add_menu_page( "InPursuit", "InPursuit", "manage_options", "inpursuit", array( $this, 'displayMenuPage' ), 'dashicons-universal-access-alt', 1 );
		}

		function displayMenuPage( $r ){
			include( 'templates/inpursuit.php' );
		}

		function setupDashboard(){
			global $wp_meta_boxes;
			$wp_meta_boxes['dashboard']['normal']['core'] = array();
	    $wp_meta_boxes['dashboard']['side']['core'] = array();
			unset( $wp_meta_boxes['dashboard']['normal']['high'] );

			wp_add_dashboard_widget( 'dashboard_widget', 'Recent Members', function(){
				include( 'templates/dashboard.php' );
			} );

			wp_add_dashboard_widget( 'dashboard_history', 'Recent Events', function(){
				include( 'templates/dashboard-history.php' );
			} );

			wp_add_dashboard_widget( 'dashboard_map', 'Map', function(){
				include( 'templates/dashboard-map.php' );
			} );



		}

		function assets( $hook ) {
			if( $hook == 'index.php' ){
				wp_enqueue_style( 'inpursuit-dashboard', plugins_url( 'InPursuit/dist/css/dashboard.css' ), array(), INPURSUIT_VERSION );

				wp_enqueue_style( 'choropleth', plugins_url( 'InPursuit/dist/css/choropleth.css' ), array(), INPURSUIT_VERSION );
				wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.css', array(), INPURSUIT_VERSION );
		 	 	wp_enqueue_style( 'leaflet-marker', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css', array(), INPURSUIT_VERSION );
		 	 	wp_enqueue_style( 'leaflet-marker-default', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css', array(), INPURSUIT_VERSION );

				wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.4.0/dist/leaflet.js', array( 'jquery' ), INPURSUIT_VERSION , true );
		 	 	wp_enqueue_script( 'leaflet-marker', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js', array( 'jquery', 'leaflet' ), INPURSUIT_VERSION , true );
		 	 	wp_enqueue_script( 'leaflet-csv', plugins_url( 'InPursuit/dist/js/leaflet.geocsv.js' ), array( 'leaflet' ), INPURSUIT_VERSION , true );
		 	 	wp_enqueue_script( 'sow-choropleth', plugins_url( 'InPursuit/dist/js/choropleth.js' ), array( 'jquery', 'leaflet-csv', 'leaflet-marker' ), INPURSUIT_VERSION , true );
			}
		}

		function getMapJsons(){
	    $jsons = array(
				'countries' => plugins_url( 'InPursuit/dist/js/map/countries.json' ) ,
				'india' 		=> plugins_url( 'InPursuit/dist/js/map/india.json' )
			);
	    return apply_filters( 'inpursuit-map-jsons', $jsons );;
	  }

		function combineMapJsons(){
	    $data = array();

	    $jsons = $this->getMapJsons();
			foreach( $jsons as $key => $json_file ){
	      $strJsonFileContents = file_get_contents( $json_file );

	      // Convert to array
	      $data[ $key ] = json_decode( $strJsonFileContents, true );
	    }

	    echo wp_json_encode( $data );
			wp_die();
	  }

	}

	INPURSUIT_ADMIN_UI::getInstance();
