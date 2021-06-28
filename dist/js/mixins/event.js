var endpoints = require( '../lib/endpoints.js' );

module.exports = {
	data(){
		return {
			url					: endpoints.events,
			filterTerms	: {
				event_type : {
					slug	: 'event_type',
					label	: 'All Event Types',
				},
				location : {
					slug	: 'location',
					label	: 'All Locations',
				}
			},
		}
	},
};
