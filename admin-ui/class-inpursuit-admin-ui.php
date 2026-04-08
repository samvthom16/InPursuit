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


	}

	INPURSUIT_ADMIN_UI::getInstance();
