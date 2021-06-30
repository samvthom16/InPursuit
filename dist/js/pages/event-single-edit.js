var defaultMixin = require( '../mixins/default.js' );
var postEditMixin = require( '../mixins/post-edit.js' );
var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );


module.exports = Vue.component( 'inpursuit-member-edit', {
	mixins	: [ defaultMixin, postEditMixin ],
	data(){
		return {
			dropdowns	: [
				{ label: 'Event type', field: 'event_type' },
				{ label: 'Location', field: 'location' }
			],
			post_type		: 'events',
			labels : {
				title: "Event Title",
				content: "Event Description"
			}
		}
	},
} );
