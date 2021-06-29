<?php

	class INPURSUIT_DB_USER extends INPURSUIT_BASE{

		private $taxonomy;

		function __construct(){
			$this->setTaxonomy( 'inpursuit-group' );

			// LIMIT MEMBERS INFORMATION IF THE USER GROUP HAS BEEN SET IN THE USER PROFILE

			add_action( 'pre_get_posts', function( $query ){

				if( isset( $query->query ) && isset( $query->query['post_type'] ) && $query->query['post_type'] == INPURSUIT_MEMBERS_POST_TYPE ){
					$tax_query = $this->getTaxQueryForCurrentUser();
					if( count( $tax_query ) ){
						$query->set( 'tax_query', array( $tax_query ) );
						//$this->test( $tax_query );
					}

				}

			} );
		}

		function getTaxonomy(){ return $this->taxonomy; }
		function setTaxonomy( $taxonomy ){ $this->taxonomy = $taxonomy; }

		function getLimitedGroups( $user_id ){
			$taxonomy = $this->getTaxonomy();
			return is_array( get_user_meta( $user_id, $taxonomy, true ) ) ? get_user_meta( $user_id, $taxonomy, true ) : array();
		}

		function getTaxQueryForCurrentUser(){
			$tax_query_arr = array();
			$user_id = get_current_user_id();
			$limited_groups = $this->getLimitedGroups( $user_id );
			if( count( $limited_groups ) ){
				$tax_query_arr = array(
					'taxonomy'	=> $this->getTaxonomy(),
					'field'			=> 'id',
					'terms'			=> $limited_groups
				);
			}
			return $tax_query_arr;
		}


	}

	INPURSUIT_DB_USER::getInstance();
