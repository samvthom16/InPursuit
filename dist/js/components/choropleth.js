var API = require( '../lib/api.js' );

var endpoints = require( '../lib/endpoints.js' );

module.exports = Vue.component( 'inpursuit-choropleth-map', {
	template: "<div data-behaviour='choropleth-map'>" +
		"<span class='inpursuit-spinner spinner' :class='{active: loading}'></span>" +
		"<div id='map'></div></div>",
	data(){
		return {
			loading		: true,
			data 			: {},
			map_jsons : {},
		}
	},
	methods: {
		drawMarkers: function( map ){

			var data = this.data;

			if( data['markers'] == undefined ) return '';

			var markersLayer = [];
			var markersClusterGroup = L.markerClusterGroup( {
				iconCreateFunction: function(cluster) {
					var count = 0;
					var child_markers = cluster.getAllChildMarkers();
					for( var key in child_markers ){
						if( child_markers[key].options.icon.options.count != undefined ){
							count += parseInt( child_markers[key].options.icon.options.count );
						}
					}
					return L.divIcon( { className	: 'inpursuit-icon', html : "<span>" + count + "</span>" } );
				}
			} );

			for( var key in data['markers'] ){

				var marker = data['markers'][key];

				if( marker['lat'] != undefined && marker['lng'] != undefined ){
					var icon = L.divIcon( {
						className	: 'inpursuit-icon',
						html			: "<span>" + marker['html'] + "</span>",
						iconUrl 	: marker['icon'],
						count			: marker['html']
					} );

					// ADD MARKER BASED ON LAT AND LNG
					var markerLayer = L.marker( [ marker['lat'], marker['lng'] ], { icon: icon } );

					// ADD LINK FOR THE MARKER IF IT EXISTS
					if( marker['link'] != undefined ){
						markerLayer.on( 'click', function(e){
							window.open( marker['link'] );
						} );
					}

					// ADD POPUP FOR THE MARKER IF IT EXISTS
					if( marker['popup'] != undefined ){ markerLayer.bindPopup( marker['popup'] ); }
					markersClusterGroup.addLayer( markerLayer );
				}
			}
			markersClusterGroup.addTo( map );
		},
		styleRegion: function( feature ){
			return {
				fillColor		: '#311B92',
				weight			: 1,
				opacity			: 0.4,
				color				: 'black',
				dashArray		: '1',
				fillOpacity	: 0.8
			};
		},
		drawRegions: function( map, geoRegions ){
			var data = this.data;

			// DRAW BOOUNDARY LINES
			var gjLayerRegionLines = L.geoJson( geoRegions, { style: {
				"color"				: data['region-lines']['color'] ? data['region-lines']['color'] : '#000000',
				"weight"			: data['region-lines']['weight'] ? data['region-lines']['weight'] : 1,
				"opacity"			: data['region-lines']['opacity'] ? data['region-lines']['opacity'] : 1,
				"fillColor"		: '#ffffff',
				'fillOpacity'	: 0.8,
			} } );
			gjLayerRegionLines.addTo( map );

			// STYLE THE REGIONS THAT ARE SELECTED WITH SOME COLOR
			var gjLayerRegion = L.geoJson( geoRegions, { style: this.styleRegion, filter: function( feature ){ return false; } } );
			gjLayerRegion.addTo( map );
		},
		drawMap : function(){

			var data = this.data;

			var zoomLevel = data['map']['desktop']['zoom'],
				center_lat	= data['map']['desktop']['lat'],
				center_lng 	= data['map']['desktop']['lng'];

			var window_width = jQuery( window ).width();
			if( window_width < 500 ){ zoomLevel = data['map']['mobile']['zoom']; }
			else if( window_width < 768 ){ zoomLevel = data['map']['tablet']['zoom']; }

			//SETUP BASEMAP
			var map = L.map('map').setView( [center_lat, center_lng], zoomLevel );

			if( data['map']['base_url'] == undefined ){
				data['map']['base_url'] = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}';
			}

			var hyb = new L.TileLayer( data['map']['base_url'], {minZoom: zoomLevel, maxZoom: 18, attribution: data['map']['attribution'], opacity:1}).addTo(map);

			// DRAW THE LINES FOR EACH REGION AND COLOR ONLY THE SELECTED AREAS
			for( var slug in this.map_jsons ){
				this.drawRegions( map, this.map_jsons[ slug ] );
			}

			this.drawMarkers( map );
		},
		getRegionsData: function(){
			var component = this;
			API.request( {
				url					: endpoints.regions,
				callbackFn	: function( response ){
					component.map_jsons = response.data;
					component.drawMap();
					component.loading = false;
				}
			} );
		},
		getMapData: function(){
			var component = this;
			API.request( {
				url					: endpoints.map,
				callbackFn	: function( response ){
					component.data = response.data;
				}
			} );
		}
	},
	created: function(){
		this.getMapData();
		this.getRegionsData();
	}
} );
