<?php

class INPURSUIT_DB extends INPURSUIT_DB_BASE{

	function eventsQuery( $member_id = 0 ){
		global $wpdb;
		$post_type = INPURSUIT_EVENTS_POST_TYPE;
		$posts_table = $wpdb->posts;

		require_once('class-inpursuit-db-event-member-relation.php');
		$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
		$event_member_table = $event_member_db->getTable();

		$query = "SELECT post_title as text, post_date, 'event' as type FROM $posts_table WHERE post_status='publish' AND post_type='$post_type'";
		if( $member_id ){
			$query .= " AND ID IN (SELECT event_id FROM $event_member_table WHERE member_id=$member_id)";
		}
		return $query;
	}

	function commentsQuery( $member_id = 0 ){
		require_once('class-inpursuit-db-comment.php');
		$comment_table = INPURSUIT_DB_COMMENT::getInstance()->getTable();

		$query = "SELECT comment as text, modified_on as post_date, 'comment' as type FROM $comment_table";
		if( $member_id ){
			$query .= " WHERE post_id=$member_id";
		}
		return $query;
	}

	function getHistory( $args ){
		global $wpdb;
		$wp_util = INPURSUIT_WP_UTIL::getInstance();

		$page = isset( $args[ 'page' ]  ) ? $args[ 'page' ] : 1;
		$per_page = isset( $args[ 'per_page' ]  ) ? $args[ 'per_page' ] : 10;

		$member_id = isset( $args['id'] ) ? $args['id'] : 0;

		$events_query = $this->eventsQuery( $member_id );
		$comments_query = $this->commentsQuery( $member_id );

		$query = "$comments_query UNION ALL $events_query";

		$countquery = "SELECT count(*) FROM ( $query ) history";

		$mainquery = $query . " ORDER BY post_date DESC " . $this->_limit_query( $page, $per_page );

		$data = array();

		$rows = $wpdb->get_results( $mainquery );
		$total_count = $wpdb->get_var( $countquery );
		$total_pages = ceil( $total_count/$per_page );
		foreach( $rows as $row ){
			$post = array(
				'title'				=> array( 'rendered' => $row->text ),
				'date'				=> $row->post_date,
				'type'				=> $row->type,
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

INPURSUIT_DB::getInstance();
