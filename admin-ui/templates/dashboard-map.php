<?php

function browserData( $type, $data ){
	?>
	<script type="text/javascript">
	if( window.browserData === undefined || window.browserData[ '<?php _e( $type );?>' ] === undefined ){
		var data = window.browserData = window.browserData || {};
		browserData[ '<?php _e( $type );?>' ] = <?php echo json_encode( wp_unslash( $data ) );?>;
	}
	</script>
	<?php
}



//print_r( $terms );

//browserData( 'sp_map_data', $map_data );
?>

<div id="inpursuit-map">
	<inpursuit-choropleth-map></inpursuit-choropleth-map->
</div>



<div data-behaviour="choropleth-map"></div>
