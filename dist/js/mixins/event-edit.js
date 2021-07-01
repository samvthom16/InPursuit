var defaultMixin = require( '../mixins/default.js' );
var postEditMixin = require( '../mixins/post-edit.js' );

module.exports = {
	mixins	: [ defaultMixin, postEditMixin ],
	data(){
		return {
			dropdowns	: [
				{ label: 'Event type', field: 'event_type' },
				{ label: 'Location', field: 'location' }
			],
			post_type		: 'events',
			labels : {
				title		: "Event Title",
				date		: "Event Date",
				content	: "Event Description"
			}
		}
	},
}
