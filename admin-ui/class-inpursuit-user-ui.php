<?php


class INPURSUIT_USER_UI extends INPURSUIT_BASE{

	//private $taxonomy;

	function __construct(){

		//$this->setTaxonomy( 'inpursuit-group' );

		/* SHOW EXTRA FIELDS */
		add_action( 'show_user_profile', array( $this, 'extraUserFields' ) );
		add_action( 'edit_user_profile', array( $this, 'extraUserFields' ) );

		/* SAVE EXTRA FIELDS */
		add_action( 'personal_options_update', array( $this, 'saveExtraUserFields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'saveExtraUserFields' ) );

		// REASSIGN USER-ID IN IP_COMMENTS TABLE
    add_action( 'delete_user', array( $this, 'reassignUserData' ), 10, 2 );
	}

	//function getTaxonomy(){ return $this->taxonomy; }
	//function setTaxonomy( $taxonomy ){ $this->taxonomy = $taxonomy; }

	function extraUserFields( $user ){
		include( "templates/user-fields.php" );
	}

	function saveExtraUserFields( $user_id ){
		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$user_db = INPURSUIT_DB_USER::getInstance();
		$taxonomy = $user_db->getTaxonomy();

		if( isset( $_POST[ $taxonomy ] ) && is_array( $_POST[ $taxonomy ] ) ){
			update_user_meta( $user_id, $taxonomy, $_POST[ $taxonomy ] );
		}
		else{
			delete_user_meta( $user_id, $taxonomy );
		}

	}

	function reassignUserData( $user_id, $reassigned_user_id ){

		$reassigned_user = new WP_User( $reassigned_user_id );

		// RETURN IF REASSIGNED USER IS INVALID
		if(	null === $reassigned_user_id || !$reassigned_user->exists() ) return;

		global $wpdb;
		$ip_comments_table = $wpdb->prefix.'ip_comments';
		$ip_comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $ip_comments_table WHERE user_id = %d", $reassigned_user_id ) );
		$wpdb->update( $ip_comments_table, array( 'user_id' => $reassigned_user_id ), array( 'user_id' => $user_id ) );
	}

}

INPURSUIT_USER_UI::getInstance();
