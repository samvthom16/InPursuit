<?php
/*
* Model: EVENT
*/

class INPURSUIT_DB_EVENT extends INPURSUIT_DB_BASE{

	function __construct(){

		parent::__construct();

		$this->setPostType( INPURSUIT_EVENTS_POST_TYPE );

		$this->setPostTypeOptions( array(
			'name' 					=> 'Events',
			'singular_name' => 'Event',
			'slug' 					=> $this->getPostType(),
			'description' 	=> 'Holds our events specific data',
			'menu_icon'			=> 'dashicons-format-video',
			'supports'			=> array( 'title', 'editor', 'thumbnail' )
		) );

	}

	function getHistory( $args ){
		$page = isset( $args[ 'page' ]  ) ? $args[ 'page' ] : 1;
		$per_page = isset( $args[ 'per_page' ]  ) ? $args[ 'per_page' ] : 10;


		$post_type = $this->getPostType();

		require_once('class-inpursuit-db-event-member-relation.php');
		$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
		$wp_util = INPURSUIT_WP_UTIL::getInstance();

		global $wpdb;
		$posts_table = $wpdb->posts;
		$event_member_table = $event_member_db->getTable();


		$subquery = " FROM $posts_table WHERE post_status='publish' AND post_type='$post_type'";

		if( isset( $args['id'] ) ){
			$member_id = $args['id'];
			$subquery .= " AND ID IN (SELECT event_id FROM $event_member_table WHERE member_id=$member_id)";
		}
		//$subquery = " FROM " . $wpdb->posts . " WHERE ID IN (SELECT event_id FROM " . $event_member_db->getTable() . " WHERE member_id=$member_id) AND post_status='publish' AND post_type='$post_type' ORDER BY post_date DESC";
		$subquery .= " ORDER BY post_date DESC";

		$mainquery = "SELECT *" . $subquery . $this->_limit_query( $page, $per_page );
		$countquery = "SELECT count(*)" . $subquery;

		$data = array();


		$rows = $wpdb->get_results( $mainquery );
		$total_count = $wpdb->get_var( $countquery );
		$total_pages = ceil( $total_count/$per_page );
		foreach( $rows as $row ){
			$post = array(
				'title'			=> array( 'rendered' => $row->post_title ),
				'date'			=> $row->post_date,
				'date_gmt'	=> $row->post_date_gmt,
				'terms'			=> $wp_util->getAllTermsForPost( $row->ID )
			);
			array_push( $data, $post );
		}


		return array(
			'data'				=> $data,
			'total'				=> $total_count,
			'total_pages'	=> $total_pages
		);
	}



}
INPURSUIT_DB_EVENT::getInstance();
