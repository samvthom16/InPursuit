Vue.use( Dropdown );
Vue.use( VueRouter );

var routes = require( './lib/routes.js' );

var router = new VueRouter( { routes } );

var components = [ 'choropleth', 'comments', 'dropdown', 'event-card', 'event-progress', 'featured-image', 'latest-updates', 'member-card',
'pagination', 'search-text', 'special-event', 'timeline-event', 'timeline' ];
for( var key in components ){
	require( './components/' + components[key] + ".js" );
}

new Vue( { el: '#inpursuit-app', router: router } );
