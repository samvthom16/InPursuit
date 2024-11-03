<?php

/* BASE CLASS TO CREATE CUSTOM TAXONOMIES */
class INPURSUIT_TAXONOMIES extends INPURSUIT_BASE{

	private $location_fields;

  function __construct(){
    add_action( 'init', array( $this, 'init' ) );

		$this->setLocationFields( array(
			'lat'	=> 'Latitude',
			'lng'	=> 'Longitude'
		) );

		// Add the fields to the "presenters" taxonomy, using our callback function
		add_action( 'inpursuit-location_edit_form_fields', array( $this, 'locationExtraFields' ), 10, 2 );

		add_action( 'edited_inpursuit-location', array( $this, 'saveLocationExtraFields' ), 10, 2 );
  }

	function getLocationFields(){ return $this->location_fields; }
	function setLocationFields( $location_fields ){ $this->location_fields = $location_fields; }

  /* FIRES ON ACTION HOOK - INIT*/
  function init(){

    global $inpursuit_vars;

    /* CREATE TAXONOMIES */
    if( ! isset( $inpursuit_vars['taxonomies'] ) ){
      $inpursuit_vars['taxonomies'] = array();
    }

    /* HOOK TO ADD CUSTOM TAXONOMIES */
    $inpursuit_vars['taxonomies'] = apply_filters( 'inpursuit_taxonomy_vars', $inpursuit_vars['taxonomies'] );

    /* ITERATE THROUGH THE TAXONOMIES ARRAY AND CREATE THEM */
    foreach( $inpursuit_vars['taxonomies'] as $taxonomy ){
      $this->create_taxonomy( $taxonomy );
    }

  }

  /* CREATE CUSTOM TAXONOMIES */
  function create_taxonomy( $taxonomy ) {

    $defaults = array(
      'hierarchical' 		=> true,
      'show_admin_column' => true,
      'show_ui' 			=> true,
      'show_in_menu' 		=> true
    );

    $r = wp_parse_args( $taxonomy, $defaults );

    $labels = array(
      'name' 							=> _x( $r['label'], 'taxonomy general name' ),
      'singular_name' 				=> _x( $r['label'], 'taxonomy singular name' ),
      'search_items' 					=>  __( 'Search '.$r['label'] ),
      'popular_items' 				=> __( 'Popular '.$r['label'] ),
      'all_items' 					=> __( 'All '.$r['label'] ),
      'parent_item' 					=> null,
      'parent_item_colon' 			=> null,
      'edit_item' 					=> __( 'Edit '.$r['label'] ),
      'update_item' 					=> __( 'Update '.$r['label'] ),
      'add_new_item' 					=> __( 'Add New '.$r['label'] ),
      'new_item_name' 				=> __( 'New '.$r['label'] ),
      'separate_items_with_commas' 	=> __( 'Separate '.$r['label'].' with commas' ),
      'add_or_remove_items' 			=> __( 'Add or remove '.$r['label'] ),
      'choose_from_most_used' 		=> __( 'Choose from the most used '.$r['label'] ),
      'menu_name' 					=> __( $r['label'] ),
    );

    register_taxonomy( $r['slug'], $taxonomy['post_types'], array(
      'hierarchical' 			=> $r['hierarchical'],
      'labels' 				=> $labels,
      'show_ui' 				=> $r['show_ui'],
      'show_admin_column' 	=> $r['show_admin_column'],
      'update_count_callback' => '_update_post_term_count',
      'query_var' 			=> true,
      'show_in_menu' 			=> $r['show_in_menu'],
      'rewrite' 				=> array( 'slug' => $r['slug'] ),
      'show_in_rest'		=> isset( $r['show_in_rest'] ) && $r['show_in_rest'] ? $r['show_in_rest'] : true,
    ));
  }

	function locationExtraFields( $term, $taxonomy ){

		$location_fields = $this->getLocationFields();

		?>
		<table class="form-table">
			<tbody>
				<?php foreach( $location_fields as $slug => $title ): $value = get_term_meta( $term->term_id, $slug, true );?>
				<tr class="form-field">
					<th><label for="<?php _e( $slug );?>"><?php _e( $title );?></label></th>
					<td><input type="text" name="<?php _e( $slug );?>" id="<?php _e( $slug );?>" value="<?php _e( $value );?>" /></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php
	}

	function saveLocationExtraFields( $term_id ) {
		$location_fields = $this->getLocationFields();
		foreach( $location_fields as $slug => $title ){
			update_term_meta(
				$term_id,
				$slug,
				sanitize_text_field( $_POST[ $slug ] )
			);
		}
	}

}

INPURSUIT_TAXONOMIES::getInstance();
