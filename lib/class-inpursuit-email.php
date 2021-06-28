<?php

class INPURSUIT_EMAIL extends INPURSUIT_BASE {

    public function __construct() {

        add_action( 'inpursuit_cron_email', [ $this, 'inpursuitCronEmailCb'], 10, 4);
        add_action( 'inpursuit_daily_greeting', [ $this, 'inpursuitDailyGreetingCb'], 10);

        $this->scheduleGreeting();

    }


    /**
     * Schedules daily cron email tasks
     */
    public function scheduleGreeting()
    {
        if(!wp_next_scheduled( 'inpursuit_daily_greeting' )){
            wp_schedule_single_event( time(), 'inpursuit_daily_greeting' );
        }

    }


    /**
     * action hook cb to be used in scheduling greetings
    */
    public function inpursuitDailyGreetingCb()
    {
        $grettings = INPURSUIT_GREETINGS::getInstance();
        $grettings->scheduleGreetings();
    }



    /**
     *  action hook cb to be used in scheduling cron email events
     * */ 
	function inpursuitCronEmailCb($to, $subject, $body, $header){
		wp_mail($to, $subject, $body, $header);
	}
	

	/**
     * Inursuit email function that uses cron job
     * Use this for asyncronus wp_mail behaviour
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

INPURSUIT_EMAIL::getInstance();