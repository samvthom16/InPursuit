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

			$registered_members = 100;
			$total_attending = 0;


			$event_date = explode(',', get_the_time('Y,m,d', $post_id));

			$args = array(
				'post_type' => 'inpursuit-members',
			    'date_query' => array(
			        array(
			            'before'    => array(
			                'year'  => $event_date[0],
			                'month' => $event_date[1],
			                'day'   => $event_date[2],
			            ),
			            'inclusive' => true,
			        ),
			    ),
			    'posts_per_page' => -1,
			);
			$members_query = new WP_Query( $args );


			if( isset( $members_query->post_count ) ){
				$registered_members = $members_query->post_count;
			}


			$event_member_db = INPURSUIT_DB_EVENT_MEMBER_RELATION::getInstance();
			$participating_members = $event_member_db->getMembersIDForEvent( $post_id );

			if( is_array( $participating_members ) ){
				$total_attending = count($participating_members);
			}

			$percentage = 0;
			if( $registered_members > 0 ){
				$percentage = ceil( ($total_attending / $registered_members) * 100 );
			}

			if( $percentage > 100 ) $percentage = 100;



			include ('templates/event-members-percentage.php');


		}
	}

}

INPURSUIT_EVENT_ADMIN_UI::getInstance();
