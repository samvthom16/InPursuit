
<?php
  $range_values = array();
  foreach( $atts['items'] as $name => $value ) {
    $temp = $value['name'];
    array_push( $range_values, $temp  );
  }

  if( count( $range_values ) ){
    sort( $range_values );
    $minValue = min( $range_values );
    $maxValue = max( $range_values );
  }

  $selectMinPercentageValue = 0;
  $selectMaxPercentageValue = 100;

  if( isset( $atts['value'] ) && is_array( $atts[ 'value' ]) && count( $atts['value'] ) ){

    $selectMinValue = min( $atts['value'] );
    $selectMaxValue = max( $atts['value'] );
    $steps = $maxValue - $minValue;

    $selectMinPercentageValue = intval( ( ( $selectMinValue - $minValue ) * 100 ) / $steps );
    $selectMaxPercentageValue = intval( ( ( $selectMaxValue - $minValue ) * 100 ) / $steps );

  }

?>

<?php if( count( $range_values ) ): ?>
<div data-behaviour="multirange" data-name="<?php _e( $atts['name'] );?>" data-range='<?php _e( $minValue ) ?>,<?php _e( $maxValue )?>'>
  <input type="range" multiple value="<?php _e( $selectMinPercentageValue );?>,<?php _e( $selectMaxPercentageValue );?>">
  <div class='labels'>
    <div class='min-label'></div>
    <div class='max-label'></div>
  </div>
</div>
<div class='multirange-checkboxes' style="display:none;">
<?php
  $this->display( array(
    'name'  => $atts['name'],
    'type'  => 'checkbox',
    'items' => $atts['items']
  ) );
?>
</div>
<?php endif;?>
