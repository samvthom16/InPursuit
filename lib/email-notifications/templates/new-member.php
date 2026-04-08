<?php
  $member_name = esc_html( $data['post_title'] );
  $member_date = esc_html( $data['post_date'] );
  $edit_url    = esc_url( $data['edit_url'] );
?>
<p>A new member has been added to <?php echo esc_html( get_bloginfo( 'name' ) ); ?>.</p>
<p>
  <b>Name:</b> <?php echo $member_name; ?><br>
  <b>Added:</b> <?php echo $member_date; ?>
</p>
<p><a href="<?php echo $edit_url; ?>">View Member Profile</a></p>
