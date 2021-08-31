<?php

class INPURSUIT_GREETINGS extends INPURSUIT_BASE {

    public function __construct() {
        add_action( 'inpursuit_daily_greeting', [ $this, 'dailyGreetingCb'], 10);

        $this->scheduleJob();
    }


    /**
     * Schedule daily cron job for greetings email
     */
    public function scheduleJob()
    {

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
     * action hook callback to be used in scheduling greetings
    */
    public function dailyGreetingCb()
    {
        $this->scheduleGreetings();
    }


    public function scheduleGreetings()
    {
        $member_model = INPURSUIT_DB_MEMBER_DATES::getInstance();

        $members_events = $member_model->getMembersEventForToday();

        foreach ($members_events as $event => $members) {
            $this->sendGreetings($members, $event);
        }
    }


    public function sendGreetings($members, $event)
    {
        $mailer = INPURSUIT_MAILER::getInstance();

        foreach ( $members as $member ) {

            $greeting = $this->prepareGreeting($member, $event);

            if( isset( $greeting['to'] ) ) {
              $mailer->sendEmail( $greeting['to'], $greeting['subject'], $greeting['body'] );
            }

        }
    }

    public function prepareGreeting($member, $event)
    {
        $member_meta = get_post_meta($member->member_id);
        $member_email = $member_meta['email'][0];

        // exit with empty array if member doesn't has email address
        if( $member_email == '' ) return [];

        $member_name = get_the_title( $member->member_id );
        $template_vars = [ '$name' => $member_name ];

        $template = get_option( 'inpursuit_settings_template_' . strtolower( $event ) );
        $subject = get_option( 'inpursuit_settings_subject_' . strtolower( $event ) );
        $body = strtr( $template, $template_vars );

        //$from = get_option('inpursuit_settings_email_from');
        //$cont_type = 'Content-Type: text/html; charset=UTF-8';

        $greeting = [
            'to'        => $member_email,
            'subject'   => $subject,
            'body'      => $body,
            //'headers'   => [ $cont_type, $from ]
        ];

        return $greeting;
    }

}
