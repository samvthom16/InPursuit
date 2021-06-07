<?php

class INPURSUIT_MEMBER_ADMIN_UI extends INPURSUIT_POST_ADMIN_UI_BASE{

	var $post_type;

	function __construct(){
		$this->setPostType( 'inpursuit-members' );

		$this->setMetaBoxes( array(
			array(
				'id'				=> 'inpursuit-member-history',
				'title'			=> 'History',
				'supports'	=>	array('editor')
			),
		) );

		parent::__construct();
	}

	function miscActionsDiv( $post ){

		$post_type = get_post_type( $post );

		if( $post_type != $this->getPostType() ) return '';

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
		 remove_meta_box( 'inpursuit-genderdiv', $this->getPostType(), 'side' );
		 remove_meta_box( 'inpursuit-groupdiv', $this->getPostType(), 'side' );
		 remove_meta_box( 'inpursuit-statusdiv', $this->getPostType(), 'side' );
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
