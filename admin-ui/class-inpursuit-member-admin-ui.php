<?php

class INPURSUIT_MEMBER_ADMIN_UI extends INPURSUIT_BASE{

	var $post_type;

	function __construct(){

		$this->post_type = 'inpursuit-members';

		add_action( 'admin_menu', array( $this, 'removeMetaBoxes' ), 100 );

		add_action( 'post_submitbox_misc_actions', array( $this, 'miscActionsDiv' ) );

		/* ENQUEUE SCRIPTS ON ADMIN DASHBOARD */
		add_action( 'admin_enqueue_scripts', array( $this, 'assets') );

	}

	function assets( $hook ) {
		global $post_type;
		if( $post_type == $this->post_type ){
			wp_enqueue_style( 'inpursuit-admin', plugins_url( 'InPursuit/dist/css/admin-style.css' ), array(), INPURSUIT_VERSION );
		}
	}

	function miscActionsDiv( $post ){

		$post_type = get_post_type( $post );

		if( $post_type != $this->post_type ) return '';

		$taxonomies = array(
			array(
				'slug'	=> 'inpursuit-status',
				'title'	=> 'Status',
				'icon'	=> 'dashicons-performance'
			),
			array(
				'slug'	=> 'inpursuit-gender',
				'title'	=> 'Gender',
				'icon'	=> 'dashicons-admin-users'
			),
			array(
				'slug'	=> 'inpursuit-group',
				'title'	=> 'Life Group',
				'icon'	=> 'dashicons dashicons-networking'
			)
		);


		?>
		<div id="inpursuit-misc" class="misc-pub-section">
			<?php foreach( $taxonomies as $taxonomy ):?>
			<div class='inpursuit-form-field'>
				<label><span class="dashicons <?php _e( $taxonomy['icon'] );?>"></span><?php _e( $taxonomy['title'] );?></label>
				<?php echo ORBIT_FORM_FIELD::getInstance()->display( $this->getAttsForTermsDropdown( $taxonomy['slug'], $post ) );?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	function removeMetaBoxes(){
		 remove_meta_box( 'genderdiv', $this->post_type, 'side' );
		 remove_meta_box( 'life-groupdiv', $this->post_type, 'side' );
		 remove_meta_box( 'inpursuit-statusdiv', $this->post_type, 'side' );
	}

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

INPURSUIT_MEMBER_ADMIN_UI::getInstance();
