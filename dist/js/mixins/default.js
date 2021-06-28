var endpoints = require( '../lib/endpoints.js' );

var API = require( '../lib/api.js' );

module.exports = {
	data	: function(){
		return {
			settings		: {}
		}
	},
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
	methods: {
		getSettings: function(){
			var component = this;
			API.request( {
				url					: endpoints.settings,
				callbackFn	: function( response ){
					component.settings = response.data;
				}
			} );
		}
	}
};
