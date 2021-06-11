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

}

INPURSUIT_USER_UI::getInstance();
