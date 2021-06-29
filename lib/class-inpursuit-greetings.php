<?php

class INPURSUIT_GREETINGS extends INPURSUIT_BASE {
    
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
                $mailer->sendEmail( $greeting['to'], $greeting['subject'], $greeting['body'], $greeting['headers'] );
            }
           
        }
    }

    public function prepareGreeting($member, $event)
    {
        $member_meta = get_post_meta($member->member_id);
        $member_email = $member_meta['email'][0];
        
        //exit with empty array if member doesn't has email address    
        if($member_email == '') {
            return [];
        }

        $member_name = get_the_title($member->member_id);
        
        $template_key = 'inpursuit_settings_template_' . strtolower($event);
        $template = get_option($template_key);
        
        $template_vars = [ '$name' => $member_name ];

        $body = strtr($template, $template_vars);
        
        $from = get_option('inpursuit_settings_email_from');
        $cont_type = 'Content-Type: text/html; charset=UTF-8'; 
        
        $greeting = [
            'to'        => $member_email,
            'subject'   => get_option('inpursuit_settings_email_subject'),
            'body'      => $body,
            'headers'   => [ $cont_type, $from ] 
        ];
        
        return $greeting;
    }

}
