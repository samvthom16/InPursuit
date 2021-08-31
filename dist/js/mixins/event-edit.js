var defaultMixin = require( '../mixins/default.js' );
var postEditMixin = require( '../mixins/post-edit.js' );

module.exports = {
	mixins	: [ defaultMixin, postEditMixin ],
	data(){
		return {
			hide_post: {
				featured_media: true
			},
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
