<?php
  $member_name = get_post_field( 'post_title', $data['post_id'] );
  $commentor_name = get_the_author_meta( 'display_name', $data['user_id'] );
?>
<p><?php echo ucwords( $commentor_name )." has Rescheduled comment on ".ucwords($member_name)."'s profile";?></p>
<p>
  <b>Rescheduled Date:</b>
  <br>
  <?php echo date('d-m-Y', strtotime($data['modified_on'])); ?>
</p>