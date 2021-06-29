<?php

class INPURSUIT_WP_UTIL extends INPURSUIT_BASE{

	function getAttsForTermsDropdown( $taxonomy, $post, $label = 'Select' ){

		$terms = get_terms( array(
			'taxonomy' 		=> $taxonomy,
			'hide_empty' 	=> false,
		) );

		$items = array();

		foreach( $terms as $term ){
			array_push( $items, array(
				'slug' => $term->term_id,
				'name' => $term->name
			) );
		}

		$selected_terms = get_the_terms( $post, $taxonomy );
		$selected_term = is_array( $selected_terms ) && count( $selected_terms ) && isset( $selected_terms[0] ) && isset( $selected_terms[0]->term_id ) ? $selected_terms[0]->term_id : 0;

		$atts = array(
			"default_option"	=> $label,
			"name" 						=> "tax_input[$taxonomy][]",
			"type"						=> "dropdown",
			"value"						=> $selected_term,
			"items"						=> $items
		);
		return $atts;
	}

	function getAllTermsForPost( $post_id ){
		$taxonomies = get_object_taxonomies( get_post_type( $post_id ) );
		return wp_get_object_terms( $post_id, $taxonomies );
	}

	function getTerms( $taxonomies, $args = array() ){
		$args = wp_parse_args( $args ); // Parse $args in case its a query string.
		if( !empty( $args[ 'post_types' ] ) ){
			$args['post_types'] = (array) $args['post_types'];
			add_filter( 'terms_clauses', 'wpse_filter_terms_by_cpt', 10, 3 );
			function wpse_filter_terms_by_cpt( $pieces, $tax, $args ){
				global $wpdb;
				$pieces['fields'] .=", COUNT(*) as post_count " ;
				$pieces['join'] .=" INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
																INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id ";
				$post_types_str = implode( ',', $args['post_types'] );
				$pieces[ 'where' ].= $wpdb->prepare( " AND p.post_type IN(%s) GROUP BY t.term_id", $post_types_str );
				remove_filter( current_filter(), __FUNCTION__ );
				return $pieces;
			}
		}
		return get_terms($taxonomies, $args);
	}
}
INPURSUIT_WP_UTIL::getInstance();
