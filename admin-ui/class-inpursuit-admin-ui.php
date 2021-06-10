<?php

	class INPURSUIT_ADMIN_UI extends INPURSUIT_BASE{

		function __construct(){
			add_action( 'wp_dashboard_setup', array( $this, 'setupDashboard' ), 9999 );
		}

		function setupDashboard(){
			global $wp_meta_boxes;
			$wp_meta_boxes['dashboard']['normal']['core'] = array();
	    $wp_meta_boxes['dashboard']['side']['core'] = array();
			unset( $wp_meta_boxes['dashboard']['normal']['high'] );
			wp_add_dashboard_widget( 'dashboard_widget', 'Recent Updates', function(){
				include( 'templates/dashboard.php' );
			} );
			wp_add_dashboard_widget( 'dashboard_history', 'History', function(){
				include( 'templates/dashboard-history.php' );
			} );
		}

	}

	INPURSUIT_ADMIN_UI::getInstance();
