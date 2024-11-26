<?php
	/*
    Plugin Name: InPursuit
    Plugin URI: https://sputznik.com

    Description:
    Author: Samuel Thomas
    Version: 1.0.1
    Author URI: https://sputznik.com
    */


	define( 'INPURSUIT_VERSION', time() );
	define( 'INPURSUIT_MEMBERS_POST_TYPE', 'inpursuit-members' );
	define( 'INPURSUIT_EVENTS_POST_TYPE', 'inpursuit-events' );

	$inc_files = array(
		'class-inpursuit-base.php',
		'db/db.php',
		'admin-ui/admin-ui.php',
		'rest-api/rest-api.php',
		'rest-authentication/rest-authentication.php',
		'lib/lib.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}


	/* PUSH INTO THE GLOBAL VARS OF ORBIT TAXNOMIES */
	add_filter( 'inpursuit_taxonomy_vars', function( $taxonomies ){

		$members_post_type = INPURSUIT_MEMBERS_POST_TYPE;
		$events_post_type = INPURSUIT_EVENTS_POST_TYPE;

		$taxonomies['inpursuit-gender']	= array(
			'label'				=> 'Gender',
			'slug' 				=> 'inpursuit-gender',
			'post_types'	=> array( $members_post_type )
		);

		$taxonomies['inpursuit-group']	= array(
			'label'				=> 'Group',
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

		$taxonomies['inpursuit-location']	= array(
			'label'				=> 'Location',
			'slug' 				=> 'inpursuit-location',
			'post_types'	=> array( $members_post_type, $events_post_type )
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

	/* CORS ENABLED FOR THE TEST VUE SITE * DIDNT WORK AS EXPECTED
	add_action('init', function(){
		$origin = get_http_origin();
    if ( $origin === 'https://inpursuit.vercel.app' ) {
			header("Access-Control-Allow-Origin: yourfrontenddomain");
			header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
			header("Access-Control-Allow-Credentials: true");
			header('Access-Control-Allow-Headers: Origin, X-Requested-With, X-WP-Nonce, Content-Type, Accept, Authorization');
			if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
				status_header(200);
				exit();
			}
    }
	} );

	add_filter( 'rest_authentication_errors', function( $errors ){
		$request_server = $_SERVER['REMOTE_ADDR'];
    $origin = get_http_origin();
    if ($origin !== 'https://inpursuit.vercel.app') return new WP_Error('forbidden_access', $origin, array(
        'status' => 403
    ));
    return $errors;
	} );
	/* CORS ENABLED FOR THE TEST VUE SITE */



	add_action( 'init', function(){
		/*
		if( !is_user_logged_in() || !is_admin() ){
			wp_redirect( admin_url() );
			exit;
		}
		*/
	} );
