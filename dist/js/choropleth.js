(function ($) {

	$.fn.choropleth_map = function(){

		return this.each(function() {

      var $el 			 = jQuery( this ),
				data				 = window.browserData['sp_map_data'] != undefined ? window.browserData['sp_map_data'] : [],
				geoCountries = {},
				regions_data = {};

			//if( $el.data('json') ){ data = $el.data( 'json' ); }

			if( data['region-lines'] == undefined ){ data['region-lines'] = {}; }

			if( data[ 'regions' ] ){ regions_data = data[ 'regions' ]; }

			// CREATE ELEMENTS ON THE FLY
      function createElements(){

        var $loader = jQuery( document.createElement( 'div' ) );
        $loader.addClass( 'loader' );
        $loader.html( "<h3 class='loadtext'><i class='fa fa-spinner fa-spin'></i> Loading data, please wait..</h3>" );
        $loader.appendTo( $el );

        var $map = jQuery( document.createElement( 'div' ) );
        $map.attr('id', 'map');
        $map.appendTo( $el );
			}

      function drawMap( map_jsons ){

				var zoomLevel = data['map']['desktop']['zoom'],
					center_lat	= data['map']['desktop']['lat'],
					center_lng 	= data['map']['desktop']['lng'];

				var window_width = jQuery( window ).width();
				if( window_width < 500 ){
					zoomLevel = data['map']['mobile']['zoom'];
				}
				else if( window_width < 768 ){
					zoomLevel = data['map']['tablet']['zoom'];
				}

				//SETUP BASEMAP
        var map = L.map('map').setView( [center_lat, center_lng], zoomLevel );

				drawBase( map, zoomLevel );

				// DRAW THE LINES FOR EACH REGION AND COLOR ONLY THE SELECTED AREAS
				jQuery.each( map_jsons, function( slug, map_json ){

					selected_data = {};

					// ITERATE EACH SELECTED DATA TO CHANGE THE STRUCTURE A BIT FOR FASTER ACCESS
					if( regions_data[ slug ] ){
						jQuery.each( regions_data[ slug ], function( i, region ){
							selected_data[ region['region'] ] = region;
						} );
					}

					drawRegions( map, map_json, selected_data );
				});


				drawMarkers( map );

			}

			function drawBase( map, zoomLevel ){
				if( data['map']['base_url'] == undefined ){
					data['map']['base_url'] = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}';
					//https://api.mapbox.com/styles/v1/mapbox/outdoors-v9/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1Ijoic2FtdnRob20xNiIsImEiOiJjanh3cWNhYWIwN2pmM2NudzNtcDV6N3VjIn0.MoTl8WNgKqxgaTUDSIDK-Q
				}

				//var hybAttrib = 'ESRI World Light Gray | Map data © <a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a> contributors & <a href="http://datameet.org" target="_blank">Data{Meet}</a>';
				var hyb = new L.TileLayer( data['map']['base_url'], {minZoom: zoomLevel, maxZoom: 18, attribution: data['map']['attribution'], opacity:1}).addTo(map);

			}

			function drawRegions( map, geoRegions, selected_data ){

				console.log(selected_data);

				// DRAW BOOUNDARY LINES
				var gjLayerRegionLines = L.geoJson( geoRegions, { style: {
					"color"		: data['region-lines']['color'] ? data['region-lines']['color'] : '#000000',
					"weight"	: data['region-lines']['weight'] ? data['region-lines']['weight'] : 1,
					"opacity"	: data['region-lines']['opacity'] ? data['region-lines']['opacity'] : 1,
					"fillColor"		: '#ffffff',
					'fillOpacity'	: 0.8,
				} } );
				gjLayerRegionLines.addTo(map);

				// STYLE THE REGIONS THAT ARE SELECTED WITH SOME COLOR
				var gjLayerRegion = L.geoJson( geoRegions, { style: styleRegion, onEachFeature: onEachRegion, filter: matchRegions } );
        gjLayerRegion.addTo(map);

				function styleRegion( feature ){
					var color = '#311B92';
					if( selected_data[ feature.properties.NAME ]['color'] ){ color = selected_data[ feature.properties.NAME ]['color']; }
					return {
	          fillColor		: color,
	          weight			: 1,
	          opacity			: 0.4,
	          color				: 'black',
	          dashArray		: '1',
	          fillOpacity	: 0.8
	        };
				}

				function onEachRegion( feature, layer ) {
	        //CONNECTING TOOLTIP AND POPUPS TO DISTRICTS
	        layer.on({
	          mouseover: highlightFeature,
	          mouseout: resetHighlight
	          //click: zoomToFeature
	        });

					var label = feature.properties.NAME;
					if( selected_data[ feature.properties.NAME ] ['label'] ){
						label = selected_data[ feature.properties.NAME ] ['label'];
					}

	        layer.bindTooltip( label, {
	          direction : 'auto',
	          className : 'countrylabel',
	          permanent : false,
	          sticky    : true
	        } );


					if( selected_data[ feature.properties.NAME ] ['popup'] ){
						layer.bindPopup( selected_data[ feature.properties.NAME ] ['popup'], { maxWidth: 500, keepInView: true } );
					}
				}

				// ONLY ADD THOSE REGIONS THAT ARE AVAILABLE IN THE DATA
				function matchRegions( feature ) {
					if( feature.properties && selected_data[ feature.properties.NAME ] ) return true;
					return false;
				}

			}

			// ITERATE THROUGH THE LIST OF MARKERS ENTERRED BY THE USER
			function drawMarkers( map ){

				if( data['markers'] != undefined ){

					var markersLayer = [];

					var markersClusterGroup = L.markerClusterGroup();

					jQuery.each( data[ 'markers' ], function( i, marker ){
						if( marker['lat'] != undefined && marker['lng'] != undefined ){

							var icon = L.divIcon({
								className	: 'inpursuit-icon',
								html			: "<span>" + marker['html'] + "</span>",
								iconUrl 	: marker['icon'],
								//iconSize	: [30, 30],
							});

							// ADD MARKER BASED ON LAT AND LNG
							var markerLayer = L.marker( [ marker['lat'], marker['lng'] ], { icon: icon } );

							// ADD POPUP FOR THE MARKER IF IT EXISTS
							if( marker['popup'] != undefined ){ markerLayer.bindPopup( marker['popup'] ); }

							// ADD MARKER TO THE LIST OF MARKERS
							//markersLayer.push( markerLayer );

							markersClusterGroup.addLayer( markerLayer );
						}

					} );

					markersClusterGroup.addTo( map );

					//L.layerGroup( markersLayer ).addTo(map);
				}
			}

			// REGION HIGHLIGHT ON MOUSEOVER
			function highlightFeature(e) {
				var layer = e.target;
				layer.setStyle( {
          weight: 3,
          color: data['region-lines']['hover_color'] ? data['region-lines']['hover_color'] : '#FFFF00',
          opacity: 0.9
        } );
        if ( !L.Browser.ie && !L.Browser.opera ) { layer.bringToFront(); }

      }

			// RESET HIGHLIGHT ON MOUSEOUT
      function resetHighlight(e) {
				var layer = e.target;
        layer.setStyle({
          weight	: 1,
          color		: 'black',
          opacity	: 0.4
        });
      }

			// PROBABLY THE MAP VARIABLE NEEDS TO BE A GLOBAL VARIABLE HERE
      function zoomToFeature(e) {
        map.fitBounds(e.target.getBounds());
      }

      // INITIALIZE FUNCTION
      function init(){

        // CREATE ALL THE DOM ELEMENTS FIRST
        createElements();

				jQuery.ajax({
				  dataType	: "json",
				  url				: data['json_url'],
				  success		: function( response ){

						// HIDE THE LOADER
		        $el.find('.loader').hide();

						// RENDER THE MAP IN THE CORRECT DOM
		        drawMap( response );
					}
				});

      }

      init();

    });
  };
}(jQuery));

jQuery(document).ready(function(){

	jQuery( '[data-behaviour~=choropleth-map]' ).each( function(){

		var $map_container = jQuery( this );

		if( !$map_container.closest( '.inline-modal' ).length ){
			$map_container.choropleth_map();
		}

	} );



});
