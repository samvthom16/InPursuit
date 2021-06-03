<?php
	/*
    Plugin Name: InPursuit
    Plugin URI: https://sputznik.com
    Description:
    Author: Samuel Thomas
    Version: 1.0.0
    Author URI: https://sputznik.com
    */


	define( 'INPURSUIT_VERSION', time() ); //1.4.7

	$inc_files = array(
		'class-inpursuit-base.php',
		'admin-ui/class-inpursuit-member-admin-ui.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}

	/* PUSH INTO THE GLOBAL VARS OF ORBIT TYPES */
	add_filter( 'orbit_post_type_vars', function( $post_types ){

		$post_types['inpursuit-members'] = array(
			'slug' 	=> 'inpursuit-members',
			'labels'	=> array(
				'name' 			=> 'Members',
				'singular_name' => 'Member',
			),
			'public'	=> false,
			'menu_icon'	=> 'dashicons-groups',
			'supports'	=> array( 'title', 'thumbnail' )
		);

		$post_types['inpursuit-events'] = array(
			'slug' 	=> 'inpursuit-events',
			'labels'	=> array(
				'name' 			=> 'Events',
				'singular_name' => 'Event',
			),
			'public'	=> false,
			'menu_icon'	=> 'dashicons-format-video',
			'supports'	=> array( 'title', 'editor', 'thumbnail' )
		);

		return $post_types;
	} );

	/* PUSH INTO THE GLOBAL VARS OF ORBIT TAXNOMIES */
	add_filter( 'orbit_taxonomy_vars', function( $taxonomies ){

		$members_post_type = 'inpursuit-members';
		$events_post_type = 'inpursuit-events';

		$taxonomies['inpursuit-gender']	= array(
			'label'				=> 'Gender',
			'slug' 				=> 'inpursuit-gender',
			'post_types'	=> array( $members_post_type )
		);

		$taxonomies['inpursuit-group']	= array(
			'label'				=> 'Life Group',
			'slug' 				=> 'inpursuit-group',
			'post_types'	=> array( $members_post_type )
		);

		$taxonomies['inpursuit-profession']	= array(
			'label'				=> 'Profession',
			'slug' 				=> 'inpursuit-profession',
			'post_types'	=> array( $members_post_type )
		);

		$taxonomies['inpursuit-status']	= array(
			'label'				=> 'Status',
			'slug' 				=> 'inpursuit-status',
			'post_types'	=> array( $members_post_type )
		);

		$taxonomies['inpursuit-event-type']	= array(
			'label'				=> 'Event Type',
			'slug' 				=> 'inpursuit-event-type',
			'post_types'	=> array( $events_post_type )
		);

		return $taxonomies;
	} );

	function current_location(){
    if (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	add_action( 'init', function(){
		if ( strpos( current_location(), '/inpursuit-members/' ) != false ) {
			status_header( 404 );
    	nocache_headers();
			wp_redirect( home_url('/') );
			exit;
		}
	} );
