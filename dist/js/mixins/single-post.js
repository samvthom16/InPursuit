var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );

module.exports = {
	data(){
		return {
			post			: {},
			post_id		: 0,
			post_type	: 'members'
		}
	},
	methods	: {
		getAPIUrl: function(){
			return endpoints[ this.post_type ] + "/" + this.post_id;
		},
		getPost: function(){
			var component = this;
			API.request( {
				url					: component.getAPIUrl(),
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		editLink: function(){
			var route  = {
				name		: "SingleMemberEdit",
				params 	: { id : this.post.id, post: this.post }
			};

			if( this.post_type == 'events' ){
				route.name = 'SingleEventEdit';
			}

			return route;
			//return '/' + this.post_type + '/' + this.post_id + '/edit';
		},
		deletePost: function( ev ){
			ev.preventDefault();
			if( confirm('Are you sure you want to delete this?') ){
				var component = this;

				API.request( {
					method	: 'delete',
					data 		: component.post,
					url			: this.getAPIUrl(),
					callbackFn: function( response ){
						// REDIRECT TO THE ARCHIVES PAGE
						component.$router.push( '/' + component.post_type + '/' );
					}
				} );
			}
		}
	},
	created: function(){

		var post_id = this.$route.params.id;
		if( post_id ){
			this.post_id = post_id;
		}

		// CHECK IF POST INFORMATION HAS BEEN PASSED IN THE ROUTE
		if( this.$route.params.post != undefined ){
			this.post = this.$route.params.post;
		}
		else{
			this.getPost();
		}


	}
};
