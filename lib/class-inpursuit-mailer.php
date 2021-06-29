<?php

class INPURSUIT_MAILER extends INPURSUIT_BASE {

    public function __construct() {

        add_action( 'inpursuit_cron_email', [ $this, 'cronEmailCb'], 10, 4);
        add_action( 'inpursuit_daily_greeting', [ $this, 'dailyGreetingCb'], 10);

        $this->scheduleNotification();
    }

    /**
     * Schedule cron jobs here
     */
    public function scheduleNotification()
    {
        
        //schedule daily cron job for greetings notification
        if(!wp_next_scheduled( 'inpursuit_daily_greeting' )){
        
            $settings_time = get_option('inpursuit_settings_cron_time');

            if($settings_time == '') {
                $timestamp = current_time( 'timestamp' );
            } else {
                $date_time = date('Y-m-d') . ' ' .$settings_time . ' +05:30';
                $dt = new DateTime($date_time);
                $timestamp = $dt->getTimestamp();
            }
            
            wp_schedule_event( $timestamp, 'daily', 'inpursuit_daily_greeting' );
        }

    }


    /**
     * action hook cb to be used in scheduling greetings
    */
    public function dailyGreetingCb()
    {
        $grettings = INPURSUIT_GREETINGS::getInstance();
        $grettings->scheduleGreetings();
    }


    /**
     *  action hook cb to be used in scheduling cron email events
     * */ 
	function cronEmailCb($to, $subject, $body, $header){
		wp_mail($to, $subject, $body, $header);
	}
	

	/**
     * Schedules each outgoing email as a cron job 
     * Use this function when asyncronus wp_mail behaviour is required
     */
	function sendEmail($to, $subject, $body, $header){

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