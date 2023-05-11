<?php

class INPURSUIT_DB extends INPURSUIT_DB_BASE{

	function eventsQuery( $member_id = 0 ){
		global $wpdb;
		$post_type = INPURSUIT_EVENTS_POST_TYPE;
		$posts_table = $wpdb->posts;

		require_once('class-inpursuit-db-event-member-relation.php');
		$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
		$event_member_table = $event_member_db->getTable();

		$query = "SELECT ID, post_title as text, '0' as post_id, post_author, post_date, 'event' as type FROM $posts_table WHERE post_status='publish' AND post_type='$post_type'";
		if( $member_id ){
			$query .= " AND ID IN (SELECT event_id FROM $event_member_table WHERE member_id=$member_id)";
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

		// COMMENTS QUERY
		require_once('class-inpursuit-db-comment.php');
		$comment_db = INPURSUIT_DB_COMMENT::getInstance();
		$comments_query = $comment_db->getResultsQuery( array( 'member_id' => $member_id ) );

		$query = "$comments_query UNION ALL $events_query";

		$countquery = "SELECT count(*) FROM ( $query ) history";

		$mainquery = $query . " ORDER BY post_date DESC " . $this->_limit_query( $page, $per_page );

		$data = array();

		$rows = $wpdb->get_results( $mainquery );
		$total_count = $wpdb->get_var( $countquery );
		$total_pages = ceil( $total_count/$per_page );

		return array(
			'data'				=> $rows,
			'total'				=> $total_count,
			'total_pages'	=> $total_pages
		);
	}

	function getFeaturedImageURL( $post_id ){
		if( has_post_thumbnail( $post_id ) ){
			$img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );
			$url = $img_arr[0];
			return $url;
		}

		$gender_terms = wp_get_object_terms( $post_id, 'inpursuit-gender', array( 'fields' => 'names' ) );
		if( count( $gender_terms ) ){

			if( $gender_terms[0] == "Male" ){

				$male_profiles = array(
					plugins_url( "InPursuit/dist/images/male-profile.png" ),
					plugins_url( "InPursuit/dist/images/asset-5.png" ),
					plugins_url( "InPursuit/dist/images/asset-6.png" ),
					plugins_url( "InPursuit/dist/images/asset-7.png" ),
					plugins_url( "InPursuit/dist/images/asset-8.png" )
				);

				$index = $post_id % 5;
				return $male_profiles[ $index ];

				//return plugins_url( "InPursuit/dist/images/male-profile.png" );
			}
			else{

				$female_profiles = array(
					plugins_url( "InPursuit/dist/images/female-profile.jpg" ),
					plugins_url( "InPursuit/dist/images/asset-1.png" ),
					plugins_url( "InPursuit/dist/images/asset-2.png" ),
					plugins_url( "InPursuit/dist/images/asset-3.png" ),
					plugins_url( "InPursuit/dist/images/asset-4.png" )
				);

				$index = $post_id % 5;
				return $female_profiles[ $index ];

				//return plugins_url( "InPursuit/dist/images/female-profile.jpg" );
			}
		}

		return plugins_url( "InPursuit/dist/images/default-profile.png" );
	}

	function queryStatsForEventType( $event_type_id, $after_date, $before_date ){
		global $wpdb;
		$db_event_member_relation = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance()->getTable();
		$db_posts = $wpdb->posts;
		$db_term_relation = $wpdb->term_relationships;
		$db_term_taxonomy = $wpdb->term_taxonomy;

		$query = "SELECT count(member_id) as total, event_id FROM $db_event_member_relation WHERE event_id IN
			(SELECT ID FROM $db_posts WHERE (post_date BETWEEN '$after_date' AND '$before_date') AND ID IN
				(SELECT object_id FROM $db_term_relation WHERE term_taxonomy_id IN
					(SELECT term_taxonomy_id FROM $db_term_taxonomy WHERE term_id=$event_type_id) ) ) GROUP BY event_id";

		return $this->get_results( $query );
	}

	function _totalStats( $grouped_stats ){
		$total = 0;
		foreach( $grouped_stats as $stat ){
			$total += $stat->total;
		}
		return $total;
	}

	function _average( $num, $denom ){
		return $denom ? $num/$denom : 0;
	}

	function _growth( $total, $prev ){
		return $total ? ( $total - $prev ) * 100 / $total : 0;
	}

	function totalStatsForEventType( $event_type_id ){
		$grouped_stats = $this->queryStatsForEventType( $event_type_id, date( 'Y-m-d', strtotime( '-180 days' ) ), date( 'Y-m-d' ) );
		$prev_grouped_stats = $this->queryStatsForEventType( $event_type_id, date( 'Y-m-d', strtotime( '-360 days' ) ), date( 'Y-m-d', strtotime( '-180 days' ) ) );

		$previous_members = $this->_totalStats( $prev_grouped_stats );
		$previous_events = count( $prev_grouped_stats );
		$previous_average = $this->_average( $previous_members, $previous_events );


		$total_members = $this->_totalStats( $grouped_stats );
		$total_events = count( $grouped_stats );
		$total_average = $this->_average( $total_members, $total_events );
		$growth = $this->_growth( $total_average, $previous_average );
		$growth_sign = $growth > 0 ? '+' : '-';

		return array(
			'previous_members' 	=> $previous_members,
			'previous_events' 	=> $previous_events,
			'previous_average'	=> round( $previous_average, 2 ),
			'total_members'			=> $total_members,
			'total_events'			=> $total_events,
			'total_average'			=> round( $total_average, 2 ),
			'growth'						=> $growth_sign . round( $growth, 2 ) . '%'
		);
	}

	function queryTotalPosts( $post_type, $post_status, $after_date, $before_date ){
		global $wpdb;
		$db_posts = $wpdb->posts;

		$query = "SELECT count(*) as total FROM $db_posts
			WHERE (post_date BETWEEN '$after_date' AND '$before_date')
			AND post_type='$post_type' AND post_status='$post_status'";

		return $this->get_var( $query );
	}

	function totalStatsForPostType( $post_type, $post_status = 'publish' ){
		$total = $this->queryTotalPosts( $post_type, $post_status, date( 'Y-m-d', strtotime( '-180 days' ) ), date( 'Y-m-d' ) );
		$prev_total = $this->queryTotalPosts( $post_type, $post_status, date( 'Y-m-d', strtotime( '-360 days' ) ), date( 'Y-m-d', strtotime( '-180 days' ) ) );

		$growth = $this->_growth( $total, $prev_total );
		$growth_sign = $growth > 0 ? '+' : '-';

		return array(
			'previous' 	=> $prev_total,
			'total'			=> $total,
			'growth'		=> $growth_sign . round( $growth, 2 ) . '%'
		);
	}


}

INPURSUIT_DB::getInstance();
