<?php

	$metafields = $this->getMetaFields();

	foreach( $metafields as $slug => $title ){
		_e( "<label>$title</label>" );
		$form_field_atts = array(
			'type' 				=> 'text',
			'name' 				=> $slug,
			'value'				=> get_post_meta( $post->ID, $slug, true ),
			'placeholder' => $title
		);
		$this->formField( $form_field_atts );
	}

?>

<?php

	$member_events_db = INPURSUIT_DB_MEMBER_DATES::getInstance();
	$event_dates = $member_events_db->getForMember( $post->ID );
	$event_types = $member_events_db->getEventTypes();

	function getEventDateFromDB( $event_dates, $event_slug ){
		return isset( $event_dates[$event_slug] ) && isset( $event_dates[$event_slug]->event_date ) ? date( "Y-m-d", strtotime( $event_dates[$event_slug]->event_date ) ) : 0;
	}

	foreach ( $event_types as $event_slug => $event_title ):?>
		<special-event title="<?php _e( $event_title );?>" value="<?php _e( getEventDateFromDB( $event_dates, $event_slug ) );?>" slug="<?php _e( "event_dates[$event_slug]" );?>"></special-event>
	<?php endforeach;?>
