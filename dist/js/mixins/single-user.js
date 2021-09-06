var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );

Vue.component( 'inpursuit-user-actions', {
	props: ['post', 'actionCallback'],
	template: `<ul class='inpursuit-user-actions'>
			<li><a href='#' class='edit' @click='actionCallback'>Edit</a></li>
		</ul>`,
} );

module.exports = {
	data(){
		return {
			post			: {},
			post_id		: 0
		}
	},
	methods	: {
		getAPIUrl: function(){
			return endpoints['users'] + "/" + this.post_id;
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
				name		: "SingleManagerEdit",
				params 	: { id : this.post.id, post: this.post }
			};
			return route;
		},
		actionCallback: function( ev ){
			ev.preventDefault();

			var action = ev.target.innerHTML;

			if( action == 'Edit' ){
				 this.$router.push( this.editLink() );
			}

		},
		metaHTML: function(){
			var html = '';
			var fields = [
				{ field : 'username', text : 'Username', type: 'meta' },
				{ field : 'email', text : 'Email', type: 'meta' },
				{ field : 'roles', text : 'Role', type: 'meta' }
			];

			for( var i=0; i<fields.length; i++ ){
				var field = fields[i]['field'];
				var type = fields[i]['type'];
				var	value = this.post[field] != 'roles' ? this.post[field] : this.post[field][0];

				if( value ){
					var text = ( fields[i]['text'] ? `<span>${fields[i]['text']} :</span> ` : "" )  + "<span>" + value + "</span>";
					var classes = 'inpursuit-manager-meta ';
					html += "<p class='" + classes + "'>" + text + "</p>";
				}
			}

			if( html ){
				html = "<h4 class='inpursuit-meta-headline'>Additional Information</h4>" + html;
			}

			return html;
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
