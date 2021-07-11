var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );

Vue.component( 'inpursuit-post-actions', {
	props: ['post', 'actionCallback'],
	template: `<ul class='inpursuit-post-actions'>
			<li v-if='post.status != "draft"'><a href='#' class='archive' @click='actionCallback'>Archive</a></li>
			<li v-if='post.status != "publish"'><a href='#' class='archive' @click='actionCallback'>Publish</a></li>
			<li><a href='#' class='edit' @click='actionCallback'>Edit</a></li>
			<li><a href='#' class='delete' @click='actionCallback'>Delete</a></li>
		</ul>`,
} );

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
		deletePost: function(){

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
		},
		editLink: function(){
			var route  = {
				name		: "SingleMemberEdit",
				params 	: { id : this.post.id, post: this.post }
			};

			if( this.post.type == 'inpursuit-events' ){
				route.name = 'SingleEventEdit';
			}
			return route;
		},
		updatePostStatus: function( newStatus ){
			this.post.status = newStatus;
			API.request( {
				method	: 'post',
				data 		: this.post,
				url			: this.getAPIUrl(),
				callbackFn: function( response ){
				}
			} );
		},
		actionCallback: function( ev ){
			ev.preventDefault();

			var action = ev.target.innerHTML;

			if( action == 'Delete' ){
				this.deletePost();
			}
			else if( action == 'Edit' ){
				 this.$router.push( this.editLink() );
			}
			else if( action == 'Archive' ){
				this.updatePostStatus( 'draft' );
			}
			else if( action == 'Publish' ){
				this.updatePostStatus( 'publish' );
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
