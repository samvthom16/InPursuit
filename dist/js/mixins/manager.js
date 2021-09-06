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
		},
		getPostLink: function( post ){
			var route = {
				name		: "",
				params 	: { id : post.id, post: post }
			};

			if( post.id ){
				route.name = "SingleManager";
			}

			return route;
		}
	},

};
