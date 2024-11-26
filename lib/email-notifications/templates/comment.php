<?php
  $member_name = get_post_field( 'post_title', $data['post_id'] );
  $commentor_name = get_the_author_meta( 'display_name', $data['user_id'] );
?>
<p><?php echo ucwords( $commentor_name )." has commented on ".ucwords($member_name)."'s profile";?></p>
<p>
  <b>Comment:</b>
  <br>
  <?php echo $data['comment']; ?>
</p>
