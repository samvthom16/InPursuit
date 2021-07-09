<?php

	class INPURSUIT_ADMIN_UI extends INPURSUIT_BASE{

		function __construct(){

			add_action( 'admin_menu', array( $this, 'adminMenu' ) );

		}

		function adminMenu(){
			add_menu_page( "InPursuit", "InPursuit", "manage_options", "inpursuit", array( $this, 'displayMenuPage' ), 'dashicons-universal-access-alt', 1 );
		}

		function displayMenuPage( $r ){
			include( 'templates/inpursuit.php' );
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
