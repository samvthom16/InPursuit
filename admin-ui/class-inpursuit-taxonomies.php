<?php

/* BASE CLASS TO CREATE CUSTOM TAXONOMIES */
class INPURSUIT_TAXONOMIES extends INPURSUIT_BASE{

  function __construct(){
    add_action( 'init', array( $this, 'init' ) );
  }

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
      'show_in_rest'		=> isset( $r['show_in_rest'] ) && $r['show_in_rest'] ? $r['show_in_rest'] : false,
    ));
  }

}

INPURSUIT_TAXONOMIES::getInstance();
