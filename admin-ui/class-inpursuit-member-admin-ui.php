<?php

class INPURSUIT_MEMBER_ADMIN_UI extends INPURSUIT_POST_ADMIN_UI_BASE{

	var $post_type;

	function __construct(){
		$this->setPostType( 'inpursuit-members' );

		add_action( 'save_post', array( $this, 'saveEventDates' ), 10,3 );

		$this->setMetaBoxes( array(
			array(
				'id'				=> 'inpursuit-member-history',
				'title'			=> 'History',
				'supports'	=>	array('editor')
			),
		) );

		parent::__construct();
	}

	function getTaxonomiesForDropdown(){
		return array(
			'inpursuit-status' 	=> 'Status',
			'inpursuit-gender' 	=> 'Gender',
			'inpursuit-group' 	=> 'Life Group',
		);
	}

	function miscActionsDiv( $post ){

		$post_type = get_post_type( $post );

		if( $post_type != $this->getPostType() ) return '';

		$fields = array();

		$member_events_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
		$event_dates = $member_events_db->getForMember( $post->ID );
		$event_types = $member_events_db->getEventTypes();
		foreach( $event_types as $slug => $title ){
			$new_event = array(
				'slug'	=> $slug,
				'title'	=> $title,
				'field'	=> 'event',
				'value'	=> ''
			);
			if( isset( $event_dates[ $slug ] ) ){
				$new_event['value'] = $event_dates[ $slug ]->event_date;
			}
			array_push( $fields, $new_event );
		}

		$taxonomies = $this->getTaxonomiesForDropdown();
		foreach( $taxonomies as $slug => $title ){
			array_push( $fields, array(
				'slug'	=> $slug,
				'title'	=> $title,
				'field'	=> 'taxonomy'
			) );
		}
		?>

		<div id="inpursuit-misc" class="misc-pub-section">
			<?php foreach( $fields as $field ):?>
			<div class='inpursuit-form-field'>
				<label><span class="dashicons <?php _e( $this->getDashIcon( $field['slug'] ) );?>"></span><?php _e( $field['title'] );?></label>
				<?php
					if( $field['field'] == 'taxonomy' ){
						$this->formField( $this->getAttsForTermsDropdown( $field['slug'], $post ) );
					}
					else{
						$form_field_atts = array(
							'type' 	=> 'date',
							'name' 	=> "event_dates[" . $field['slug'] . "]",
							'value'	=> $field['value']
						);
						$this->formField( $form_field_atts );
					}
				?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	function removeMetaBoxes(){
		$taxonomies = $this->getTaxonomiesForDropdown();
		foreach ( $taxonomies as $slug => $title ) {
			remove_meta_box( $slug . 'div', $this->getPostType(), 'side' );
		}
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

	function saveEventDates( $post_id, $post, $update ){

		if( $post->post_type == $this->getPostType() && isset( $_POST['event_dates'] ) && count( $_POST['event_dates'] ) ){

			$member_dates_db = INPURSUIT_DB_MEMBER_DATES::getInstance();

			$member_dates_db->updateToMember( $post_id, $_POST['event_dates'] );

		}
	}


}

INPURSUIT_MEMBER_ADMIN_UI::getInstance();
