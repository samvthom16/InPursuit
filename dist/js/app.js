Vue.use( VueRouter );

var routes = require( './lib/routes.js' );

var router = new VueRouter( { routes } );

var components = [ 'choropleth', 'comments', 'dropdown', 'event-card', 'event-progress', 'featured-image', 'latest-updates', 'member-card',
'pagination', 'search-text', 'select-members', 'special-event', 'timeline-event', 'timeline', 'manager-card' ];
for( var key in components ){
	require( './components/' + components[key] + ".js" );
}

window['inpursuit_settings'] = {};

new Vue( {
	el: '#inpursuit-app',
	data(){
		return { loading: true };
	},
	methods: {
		getSettings: function(){
			var component = this;
			var API = require( './lib/api.js' );
			var endpoints = require( './lib/endpoints.js' );

			API.request( {
				url					: endpoints.settings,
				callbackFn	: function( response ){
					component.loading = false;
					window['inpursuit_settings'] = response.data;
				}
			} );
		}
	},
	router: router,
	created: function(){
		this.getSettings();
	}
} );
