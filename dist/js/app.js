Vue.use( Dropdown );
Vue.use( VueRouter );

var routes = require( './lib/routes.js' );

var router = new VueRouter( { routes } );

var components = [ 'choropleth', 'comments', 'dropdown', 'event-card', 'event-progress', 'featured-image', 'latest-updates', 'member-card',
'pagination', 'search-text', 'select-members', 'select', 'special-event', 'timeline-event', 'timeline' ];
for( var key in components ){
	require( './components/' + components[key] + ".js" );
}

var API = require( './lib/api.js' );

var endpoints = require( './lib/endpoints.js' );

window['inpursuit_settings'] = {};

API.request( {
	url					: endpoints.settings,
	callbackFn	: function( response ){
		window['inpursuit_settings'] = response.data;
		new Vue( { el: '#inpursuit-app', router: router } );
	}
} );
