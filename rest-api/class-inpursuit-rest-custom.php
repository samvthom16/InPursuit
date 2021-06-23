<?php

class INPURSUIT_REST extends INPURSUIT_REST_BASE{

	function getHistoryCallback( WP_REST_Request $args ){
		$event_db 			= INPURSUIT_DB::getInstance();
		$response_data 	= $event_db->getHistory( $args );

		$data = array();

		foreach( $response_data['data'] as $row ){
			$item = array(
				'id'					=> $row->ID,
				'post_id'			=> $row->post_id,
				'user_id'			=> $row->user_id,
				'author_name'	=> get_the_author_meta( 'display_name', $row->user_id ),
				'title'				=> array( 'rendered' => $row->text ),
				'date'				=> $row->post_date,
				'type'				=> $row->type,
				'text'				=> '',
				'edit_url'		=> admin_url( 'post.php?action=edit&post=' . $row->ID )
			);


			if( $row->type == 'comment' ){
				$item['text'] = $row->text;
				$item['title']['rendered'] = "Follow-up on " . get_the_title( $row->post_id );
				$item['edit_url'] = admin_url( 'post.php?action=edit&post=' . $row->post_id );
			}

			array_push( $data, $item );
		}

		$response = new WP_REST_Response( $data );
		$response->header( 'X-WP-TotalPages', $response_data['total_pages'] );
		$response->header( 'X-WP-Total', $response_data['total'] );

		return $response;
	}

	function getSettingsCallback( WP_REST_Request $args ){
		global $inpursuit_vars;

		$data = array(
			'name' 				=> get_bloginfo( 'name' ),
			//'taxonomies'	=> $taxonomies
		);

		$taxonomies = $inpursuit_vars['taxonomies'];
		foreach( $taxonomies as $key => $taxonomy ){

			//$fieldname = str_replace( "inpursuit-", "", $key );
			$fieldname = apply_filters( 'inpursuit_rest_field', $key );

			$data[ $fieldname ] = get_terms( array(
				'taxonomy' 		=> $taxonomy['slug'],
				'hide_empty' 	=> false,
				'fields'			=> 'id=>name'
			) );
		}

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function addRestData(){
		$this->registerRoute( 'history', array( $this, 'getHistoryCallback' ) );
		$this->registerRoute( 'history/(?P<id>\d+)', array( $this, 'getHistoryCallback' ) );
		$this->registerRoute( 'settings', array( $this, 'getSettingsCallback' ) );
	}


}
INPURSUIT_REST::getInstance();
