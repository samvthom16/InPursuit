<?php
  $event_name = esc_html( $data['post_title'] );
  $event_date = esc_html( $data['post_date'] );
  $edit_url   = esc_url( $data['edit_url'] );
?>
<p>A new event has been created on <?php echo esc_html( get_bloginfo( 'name' ) ); ?>.</p>
<p>
  <b>Event:</b> <?php echo $event_name; ?><br>
  <b>Created:</b> <?php echo $event_date; ?>
</p>
<p><a href="<?php echo $edit_url; ?>">View Event</a></p>
