<?php

class INPURSUIT_MAILER extends INPURSUIT_BASE {

    public function __construct() {

        add_action( 'inpursuit_cron_email', [ $this, 'cronEmailCb'], 10, 4);

    }


    /**
     *  action hook cb to be used in scheduling cron email events
     * */
	function cronEmailCb($to, $subject, $body, $header){
		wp_mail($to, $subject, $body, $header);
	}


	/**
     * Use this function when asyncronus wp_mail behaviour is required
     * Schedules each outgoing email as a cron job
     */
	function sendEmail( $to, $subject, $body, $header = array() ){

    // DEFAULT HEADER INFORMATION
    if( count( $header ) == 0 ){

      $from_name = get_option( 'inpursuit_settings_email_from_name' );
      $from_mail = get_option( 'inpursuit_settings_email_from_address' );

      $header = array(
        'Content-Type: text/html; charset=ISO-8859-1',
        "$from_name <$from_mail>"
      );
    }

		$args = array(
				'to'		=> $to,
				'subject'	=> $subject,
				'body'		=> $body,
				'header'	=> $header
			);

		if(!wp_next_scheduled( 'inpursuit_cron_email' )){
			wp_schedule_single_event( time(), 'inpursuit_cron_email', $args );
		}
	}

}

INPURSUIT_MAILER::getInstance();
