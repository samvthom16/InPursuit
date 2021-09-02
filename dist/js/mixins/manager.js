var endpoints = require( '../lib/endpoints.js' );

module.exports = {
	data(){
		return {
			url	: endpoints.users
		}
	},
	methods: {
		getDefaultParams: function(){
			return {
				search 		: this.searchQuery,
				page			: this.page,
				per_page	: this.per_page,
				context		: 'edit'
			}
		}
	},

};
