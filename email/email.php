<?php

class INPURSUIT_EMAIL extends INPURSUIT_BASE{

  // SEND EMAIL TO ALL THE ADMINSTRATORS
  function sendCommentEmailNotification( $comment_details ){

    $member_name = get_post_field( 'post_title', $comment_details['post_id'] );
    $commentor_name = get_the_author_meta( 'display_name', $comment_details['user_id'] );
    $comment_body = $comment_details['comment']; // Comment body

    ob_start();

    include( 'templates/comment-email.php' );

    $mail = ob_get_contents();

    ob_end_clean();

    $to = $this->getInpursuitUsersEmail();

    $from = 'From: Inpursuit <stephen@sputznik.com>';
    $cont_type = 'Content-Type: text/html; charset=UTF-8';

    $headers = array( $cont_type, $from );

    $subject = 'Comment Notification From Inpursuit';

    $body = $mail;

    $response = wp_mail( $to, $subject, $body, $headers );

  }

  function getInpursuitUsersEmail(){
    $user_emails = array();

    $admins = get_users(
      array(
        'fields' => array( 'user_email' ),
        'role__in' => array('administrator'),
      )
    );

    foreach ( $admins as $admin ) {
      array_push( $user_emails, $admin->user_email );
    }

    return $user_emails;

  }

}
