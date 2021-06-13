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


function wpse57444_get_terms( $taxonomies, $args=array() ){
    //Parse $args in case its a query string.
    $args = wp_parse_args($args);

    if( !empty($args['post_types']) ){
        $args['post_types'] = (array) $args['post_types'];
        add_filter( 'terms_clauses','wpse_filter_terms_by_cpt',10,3);

        function wpse_filter_terms_by_cpt( $pieces, $tax, $args){
            global $wpdb;

            // Don't use db count
            $pieces['fields'] .=", COUNT(*) as post_count " ;

            //Join extra tables to restrict by post type.
            $pieces['join'] .=" INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
                                INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id ";

            // Restrict by post type and Group by term_id for COUNTing.
            $post_types_str = implode(',',$args['post_types']);
            $pieces['where'].= $wpdb->prepare(" AND p.post_type IN(%s) GROUP BY t.term_id", $post_types_str);

            remove_filter( current_filter(), __FUNCTION__ );
            return $pieces;
        }
    } // endif post_types set

    return get_terms($taxonomies, $args);
}



$map_data = array(
	'markers' => array(),
	"region-lines" => array(
		'color'	=> "#08438c",
		'opacity'	=> 0.5,
		"hover_color"	=> '#FFFF00'
	),
	"map"	=> array(
		"base_url" => "https:\/\/server.arcgisonline.com\/ArcGIS\/rest\/services\/Canvas\/World_Light_Gray_Base\/MapServer\/tile\/{z}\/{y}\/{x}",
		"attribution" => "",
		"desktop" 	=> array( "zoom" => 4, "lat" => "23.2599", "lng" => "82.4126" ),
		"tablet" 		=> array( "zoom" => 4, "lat" => "23.2599", "lng" => "82.4126" ),
		"mobile" 		=> array( "zoom" => 4, "lat" => "23.2599", "lng" => "82.4126" ),
	),
	"json_url" => admin_url( 'admin-ajax.php?action=sp_combine_map_jsons' )
);

$args =array(
  'hide_empty' => 0,
	'post_types' =>array( 'inpursuit-members' ),
);

$terms = wpse57444_get_terms( 'inpursuit-location', $args );

foreach( $terms as $term ){
	array_push( $map_data['markers'], array(
		'lat'		=> get_term_meta( $term->term_id, 'lat', true ),
		'lng'		=> get_term_meta( $term->term_id, 'lng', true ),
		'html'	=> $term->post_count
	) );
}

//print_r( $terms );

browserData( 'sp_map_data', $map_data );
?>
<div data-behaviour="choropleth-map"></div>
