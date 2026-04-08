<?php

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;

class INPURSUIT_PUSH_SENDER extends INPURSUIT_BASE {

	const OPTION_PUBLIC_KEY  = 'inpursuit_vapid_public_key';
	const OPTION_PRIVATE_KEY = 'inpursuit_vapid_private_key';

	function __construct() {
		add_action( 'rest_after_insert_' . INPURSUIT_MEMBERS_POST_TYPE, array( $this, 'onMemberCreated' ), 10, 3 );
		add_action( 'rest_after_insert_' . INPURSUIT_EVENTS_POST_TYPE, array( $this, 'onEventCreated' ), 10, 3 );
		add_action( 'inpursuit_comment_created', array( $this, 'onCommentCreated' ) );
	}

	function onMemberCreated( $post, $request, $creating ) {
		if ( ! $creating ) return;
		$this->sendPushToAll(
			'New Member Added',
			esc_html( $post->post_title )
		);
	}

	function onCommentCreated( $item ) {
		$member_name = esc_html( get_the_title( $item['post_id'] ) );
		$excerpt     = esc_html( wp_trim_words( $item['comment'], 10, '...' ) );
		$this->sendPushToAll( 'New Comment', "$member_name: $excerpt" );
	}

	function onEventCreated( $post, $request, $creating ) {
		if ( ! $creating ) return;
		$this->sendPushToAll(
			'New Event Created',
			esc_html( $post->post_title )
		);
	}

	function getPublicKey() {
		$this->ensureVapidKeys();
		return get_option( self::OPTION_PUBLIC_KEY );
	}

	function ensureVapidKeys() {
		if ( get_option( self::OPTION_PUBLIC_KEY ) && get_option( self::OPTION_PRIVATE_KEY ) ) {
			return;
		}

		$keys = VAPID::createVapidKeys();
		update_option( self::OPTION_PUBLIC_KEY,  $keys['publicKey'],  false );
		update_option( self::OPTION_PRIVATE_KEY, $keys['privateKey'], false );
	}

	function sendPushToAll( $title, $body ) {
		$this->ensureVapidKeys();

		$public_key  = get_option( self::OPTION_PUBLIC_KEY );
		$private_key = get_option( self::OPTION_PRIVATE_KEY );

		if ( ! $public_key || ! $private_key ) {
			return;
		}

		$db            = INPURSUIT_DB_PUSH_SUBSCRIPTION::getInstance();
		$subscriptions = $db->getAllSubscriptions();

		if ( empty( $subscriptions ) ) {
			return;
		}

		$auth = array(
			'VAPID' => array(
				'subject'    => get_bloginfo( 'url' ),
				'publicKey'  => $public_key,
				'privateKey' => $private_key,
			),
		);

		$web_push = new WebPush( $auth );
		$payload  = json_encode( array( 'title' => $title, 'body' => $body ) );

		$expired_endpoints = array();

		foreach ( $subscriptions as $row ) {
			$subscription = Subscription::create( array(
				'endpoint' => $row->endpoint,
				'keys'     => array(
					'p256dh' => $row->p256dh,
					'auth'   => $row->auth,
				),
			) );

			$report = $web_push->sendOneNotification( $subscription, $payload );

			if ( ! $report->isSuccess() ) {
				$status = $report->getResponse() ? $report->getResponse()->getStatusCode() : 0;
				// 404 / 410 means the subscription is gone
				if ( in_array( $status, array( 404, 410 ), true ) ) {
					$expired_endpoints[] = $row->endpoint;
				}
			}
		}

		foreach ( $expired_endpoints as $endpoint ) {
			$db->deleteByEndpoint( $endpoint );
		}
	}

}
