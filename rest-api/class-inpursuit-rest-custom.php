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

	function getMapCallback( WP_REST_Request $args ){

		$map_data = array(
			'markers' => array(),
			"region-lines" => array(
				'color'				=> "#08438c",
				'opacity'			=> 0.5,
				"hover_color"	=> '#FFFF00'
			),
			"map"	=> array(
				"base_url" 		=> "https:\/\/server.arcgisonline.com\/ArcGIS\/rest\/services\/Canvas\/World_Light_Gray_Base\/MapServer\/tile\/{z}\/{y}\/{x}",
				"attribution" => "InPursuit Dashboard Map",
				"desktop" 		=> array( "zoom" => 4, "lat" => "23.2599", "lng" => "82.4126" ),
				"tablet" 			=> array( "zoom" => 4, "lat" => "23.2599", "lng" => "82.4126" ),
				"mobile" 			=> array( "zoom" => 4, "lat" => "23.2599", "lng" => "82.4126" ),
			),
			"json_url" => admin_url( 'admin-ajax.php?action=sp_combine_map_jsons' )
		);

		$wp_util = INPURSUIT_WP_UTIL::getInstance();
		$terms = $wp_util->getTerms( 'inpursuit-location', array( 'hide_empty' => 0, 'post_types' => array( 'inpursuit-members' ), ) );

		foreach( $terms as $term ){
			$slug = $term->slug;
			array_push( $map_data['markers'], array(
				'lat'		=> get_term_meta( $term->term_id, 'lat', true ),
				'lng'		=> get_term_meta( $term->term_id, 'lng', true ),
				'html'	=> $term->post_count,
				'link'	=> admin_url( "edit.php?post_type=inpursuit-members&inpursuit-location=$slug"  )
			) );
		}

		$response = new WP_REST_Response( $map_data );
		return $response;
	}

	function getRegionsCallback( WP_REST_Request $args ){

		$admin_ui = INPURSUIT_ADMIN_UI::getInstance();

		$data = array();

		$jsons = $admin_ui->getMapJsons();
		foreach( $jsons as $key => $json_file ){
			$strJsonFileContents = file_get_contents( $json_file );

			// Convert to array
			$data[ $key ] = json_decode( $strJsonFileContents, true );
		}

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function addRestData(){
		$this->registerRoute( 'history', array( $this, 'getHistoryCallback' ) );
		$this->registerRoute( 'history/(?P<id>\d+)', array( $this, 'getHistoryCallback' ) );
		$this->registerRoute( 'settings', array( $this, 'getSettingsCallback' ) );
		$this->registerRoute( 'map', array( $this, 'getMapCallback' ) );
		$this->registerRoute( 'regions', array( $this, 'getRegionsCallback' ) );
	}


}
INPURSUIT_REST::getInstance();
