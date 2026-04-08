<?php

class INPURSUIT_REST extends INPURSUIT_REST_BASE{

	function getHistoryCallback( WP_REST_Request $args ){
		$event_db 			= INPURSUIT_DB::getInstance();
		$params 				= $args->get_params();

		// Validate ID parameter if provided
		if ( isset( $params['id'] ) ) {
			$id = intval( $params['id'] );
			if ( $id <= 0 ) {
				return new WP_Error( 'invalid_id', 'ID must be a positive integer', array( 'status' => 400 ) );
			}
			$params['id'] = $id;
		}

		if( !current_user_can( 'administrator' ) ){
			$current_user = wp_get_current_user();
			$current_user_id = $current_user->ID;
			$params[ 'user_id' ] = $current_user_id;
		}

		$response_data 	= $event_db->getHistory( $params );

		$data = array();

		foreach( $response_data['data'] as $row ){
			$item = array(
				'id'					=> intval( $row->ID ),
				'post_id'			=> intval( $row->post_id ),
				'user_id'			=> intval( $row->user_id ),
				'author_name'	=> esc_html( get_the_author_meta( 'display_name', $row->user_id ) ),
				'title'				=> array( 'rendered' => esc_html( $row->text ) ),
				'date'				=> esc_html( get_date_from_gmt( $row->post_date ) ),
				'type'				=> sanitize_key( $row->type ),
				'text'				=> '',
				'edit_url'		=> esc_url_raw( admin_url( 'post.php?action=edit&post=' . intval( $row->ID ) ) )
			);


			if( $row->type == 'comment' ){
				$item['text'] = wp_kses_post( $row->text );
				$item['title']['rendered'] = esc_html( "Follow-up on " . get_the_title( $row->post_id ) );
				$item['edit_url'] = esc_url_raw( admin_url( 'post.php?action=edit&post=' . intval( $row->post_id ) ) );
				$item['comments_category'] = array_map( 'intval', INPURSUIT_DB_COMMENTS_CATEGORY_RELATION::getInstance()->get_comment_categories( $item['id'] ) );
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
			'name' 							=> esc_html( get_bloginfo( 'name' ) ),
			'comments_category'	=> array_map( 'esc_html', INPURSUIT_DB_COMMENTS_CATEGORY::getInstance()->generate_settings_schema() )
		);

		$taxonomies = $inpursuit_vars['taxonomies'];
		foreach( $taxonomies as $key => $taxonomy ){

			//$fieldname = str_replace( "inpursuit-", "", $key );
			$fieldname = apply_filters( 'inpursuit_rest_field', $key );

			$terms = get_terms( array(
				'taxonomy' 		=> $taxonomy['slug'],
				'hide_empty' 	=> false,
				'fields'			=> 'id=>name'
			) );

			// Escape term names
			$data[ $fieldname ] = array_map( 'esc_html', (array) $terms );
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
			$slug = sanitize_key( $term->slug );
			$lat = floatval( get_term_meta( $term->term_id, 'lat', true ) );
			$lng = floatval( get_term_meta( $term->term_id, 'lng', true ) );
			array_push( $map_data['markers'], array(
				'lat'		=> $lat,
				'lng'		=> $lng,
				'html'	=> intval( $term->post_count ),
				'link'	=> esc_url_raw( admin_url( "edit.php?post_type=inpursuit-members&inpursuit-location=" . sanitize_text_field( $slug ) ) )
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
			// Validate file exists and is readable
			if( !file_exists( $json_file ) || !is_readable( $json_file ) ){
				continue;
			}

			// Sanitize key
			$sanitized_key = sanitize_key( $key );

			$strJsonFileContents = file_get_contents( $json_file );

			// Decode and validate JSON
			$decoded = json_decode( $strJsonFileContents, true );
			if( is_array( $decoded ) ){
				$data[ $sanitized_key ] = $decoded;
			}
		}

		$response = new WP_REST_Response( $data );
		return $response;
	}

	function check_for_permissions(){

		if ( current_user_can( 'administrator' ) ||
			current_user_can( 'editor' )
		) {
			return true;
		}

		return new WP_Error(
			'rest_cannot_view',
			__( 'Sorry, you are not allowed to see this information.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	function getSpecialDates( WP_REST_Request $request ){
		$params = $request->get_params();

		// Validate pagination parameters
		$page = intval( $params['page'] ?? 1 );
		$per_page = intval( $params['per_page'] ?? 10 );

		if ( $page < 1 ) {
			return new WP_Error( 'invalid_page', 'Page must be 1 or greater', array( 'status' => 400 ) );
		}
		if ( $per_page < 1 || $per_page > 100 ) {
			return new WP_Error( 'invalid_per_page', 'Per page must be between 1 and 100', array( 'status' => 400 ) );
		}

		$member_model = INPURSUIT_DB_MEMBER_DATES::getInstance();
		return $member_model->getNextOneMonthEvents( array( 'page' => $page, 'per_page' => $per_page ) );
	}

	function addRestData(){

		$this->registerRoute( 'history', array( $this, 'getHistoryCallback' ), 'is_user_logged_in' );
		$this->registerRoute( 'history/(?P<id>\d+)', array( $this, 'getHistoryCallback' ), 'is_user_logged_in' );
		$this->registerRoute( 'settings', array( $this, 'getSettingsCallback' ), 'is_user_logged_in' );

		$this->registerRoute( 'map', array( $this, 'getMapCallback' ), 'is_user_logged_in' );
		$this->registerRoute( 'regions', array( $this, 'getRegionsCallback' ), 'is_user_logged_in' );
		$this->registerRoute( 'special-dates', array( $this, 'getSpecialDates' ), 'is_user_logged_in' );
	}


}
INPURSUIT_REST::getInstance();
