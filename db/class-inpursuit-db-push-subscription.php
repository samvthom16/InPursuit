<?php

class INPURSUIT_DB_PUSH_SUBSCRIPTION extends INPURSUIT_DB_BASE {

	function __construct() {
		$this->setTableSlug( 'push_subscriptions' );
		parent::__construct();
	}

	function create() {
		$table           = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			endpoint TEXT NOT NULL,
			p256dh TEXT NOT NULL,
			auth VARCHAR(255) NOT NULL,
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			UNIQUE KEY endpoint_prefix (endpoint(191))
		) $charset_collate;";

		$this->query( $sql );
	}

	function getByEndpoint( $endpoint ) {
		global $wpdb;
		$table = $this->getTable();
		$query = $this->prepare( "SELECT * FROM $table WHERE endpoint = %s LIMIT 1;", $endpoint );
		return $wpdb->get_row( $query );
	}

	function getAllSubscriptions() {
		global $wpdb;
		$table = $this->getTable();
		return $wpdb->get_results( "SELECT * FROM $table;" );
	}

	function deleteByEndpoint( $endpoint ) {
		global $wpdb;
		return $wpdb->delete(
			$this->getTable(),
			array( 'endpoint' => $endpoint ),
			array( '%s' )
		);
	}

	function upsert( $user_id, $endpoint, $p256dh, $auth ) {
		$existing = $this->getByEndpoint( $endpoint );
		if ( $existing ) {
			return $existing->ID;
		}
		return $this->insert( array(
			'user_id'  => intval( $user_id ),
			'endpoint' => $endpoint,
			'p256dh'   => $p256dh,
			'auth'     => $auth,
		) );
	}

}
