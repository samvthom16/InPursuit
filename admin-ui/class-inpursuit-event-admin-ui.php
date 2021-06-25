<?php

class INPURSUIT_EVENT_ADMIN_UI extends INPURSUIT_POST_ADMIN_UI_BASE{

	var $post_type;

	function __construct(){
		$this->setPostType( INPURSUIT_EVENTS_POST_TYPE );

		$this->setMetaBoxes( array(
			array(
				'id'				=> 'inpursuit-event-members',
				'title'			=> 'Members',
				'supports'	=>	array('editor')
			),
		) );

		$this->setTaxonomiesForDropdown( array(
			'inpursuit-event-type' 	=> 'Event Type',
			'inpursuit-location' 		=> 'Location',
		) );


		add_filter('manage_'. $this->post_type . '_posts_columns', [$this, 'columnFilterCb']);

		add_action('manage_'. $this->post_type .'_posts_custom_column', [$this, 'columnActionCb'], 10,2);

		parent::__construct();
	}


	public function columnFilterCb($columns)
	{
		$columns = array_merge(
			$columns,
			array(
				'inpursuit-event-attendance' => 'Participation')
		);

		$ordered_columns = array();

		$move = 'inpursuit-event-attendance';

		$before = 'taxonomy-inpursuit-location';

		foreach($columns as $key => $value) {

			if ($key==$before){
			  $ordered_columns[$move] = $move;
			}
			  $ordered_columns[$key] = $value;
		}

		return $ordered_columns;
	}

	public function columnActionCb($column_key, $post_id)
	{

		if ($column_key == 'inpursuit-event-attendance') {

			$event_db = INPURSUIT_DB_EVENT::getInstance();

			$percentage = $event_db->attendantsPercentage( $post_id );

			include ('templates/event-members-percentage.php');


		}
	}

}

INPURSUIT_EVENT_ADMIN_UI::getInstance();
