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


}


INPURSUIT_WP_UTIL::getInstance();
