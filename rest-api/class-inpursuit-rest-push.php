<?php

class INPURSUIT_REST_PUSH extends INPURSUIT_BASE {

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'registerRoutes' ) );
	}

	function registerRoutes() {
		$namespace = 'inpursuit/v1';

		register_rest_route( $namespace, '/push/vapid-public-key', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'getVapidPublicKey' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( $namespace, '/push/subscribe', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'subscribe' ),
			'permission_callback' => 'is_user_logged_in',
		) );

		register_rest_route( $namespace, '/push/unsubscribe', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'unsubscribe' ),
			'permission_callback' => 'is_user_logged_in',
		) );
	}

	function getVapidPublicKey( WP_REST_Request $request ) {
		$sender     = INPURSUIT_PUSH_SENDER::getInstance();
		$public_key = $sender->getPublicKey();

		if ( ! $public_key ) {
			return new WP_Error( 'vapid_not_ready', 'VAPID keys not yet generated.', array( 'status' => 503 ) );
		}

		return new WP_REST_Response( array( 'publicKey' => $public_key ) );
	}

	function subscribe( WP_REST_Request $request ) {
		$params   = $request->get_json_params();
		$endpoint = isset( $params['endpoint'] ) ? sanitize_text_field( $params['endpoint'] ) : '';
		$p256dh   = isset( $params['keys']['p256dh'] ) ? sanitize_text_field( $params['keys']['p256dh'] ) : '';
		$auth     = isset( $params['keys']['auth'] ) ? sanitize_text_field( $params['keys']['auth'] ) : '';

		if ( ! $endpoint || ! $p256dh || ! $auth ) {
			return new WP_Error( 'missing_fields', 'endpoint, keys.p256dh, and keys.auth are required.', array( 'status' => 400 ) );
		}

		$user_id = get_current_user_id();
		$db      = INPURSUIT_DB_PUSH_SUBSCRIPTION::getInstance();
		$id      = $db->upsert( $user_id, $endpoint, $p256dh, $auth );

		return new WP_REST_Response( array( 'id' => intval( $id ) ), 201 );
	}

	function unsubscribe( WP_REST_Request $request ) {
		$params   = $request->get_json_params();
		$endpoint = isset( $params['endpoint'] ) ? sanitize_text_field( $params['endpoint'] ) : '';

		if ( ! $endpoint ) {
			return new WP_Error( 'missing_endpoint', 'endpoint is required.', array( 'status' => 400 ) );
		}

		$db      = INPURSUIT_DB_PUSH_SUBSCRIPTION::getInstance();
		$deleted = $db->deleteByEndpoint( $endpoint );

		return new WP_REST_Response( array( 'deleted' => (bool) $deleted ) );
	}

}

INPURSUIT_REST_PUSH::getInstance();
