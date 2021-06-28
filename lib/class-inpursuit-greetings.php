<?php


class INPURSUIT_GREETINGS extends INPURSUIT_BASE {

    private $mailer;

    public function scheduleGreetings()
    {
        $member_event = INPURSUIT_DB_MEMBER_DATES::getInstance();

        $events = $member_event->getMembersEventForToday();
        
        foreach ($events as $key => $rows) {
            if ( 'birthday' == $key) {
                $this->scheduleBirthdayGreetings($rows);
            } elseif ( 'wedding' ) {
                $this->scheduleWeddingGreetings($rows);    
            }
        }
    }

    public function scheduleBirthdayGreetings($members)
    {
        
        $mailer = INPURSUIT_EMAIL::getInstance();

        foreach ($members as $member) {
            // $to = 'jay@test.com';
            // $from = 'admin@inpursuit.com';
            // $subject = 'test mail';
            // $body = 'bday mail'. $member->member_id;
            // $cont_type = 'Content-Type: text/html; charset=UTF-8'; 
            // $headers = array($cont_type, $from);

            // $mailer->sendEmail($to, $subject, $body, $headers);
        }

        
    }

    public function scheduleWeddingGreetings($members)
    {
        //echo "<pre>"; print_r($members); echo "</pre>"; wp_die(); 

    }


}
