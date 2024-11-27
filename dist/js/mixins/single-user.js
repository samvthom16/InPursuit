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
				params			: { context: 'edit' },
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		getGroups: function(){
			var group_names 		 = [];
			var user_groups 		 = this.post?.group ?? [];
			var available_groups = window?.inpursuit_settings?.group ?? {};

			for(var group of user_groups ){
				group_names.push(available_groups[group]);
			}

			return group_names;
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
				{ field : 'username', text : 'Username' },
				{ field : 'email', text : 'Email' },
				{ field : 'roles', text : 'Role' },
				{ field : 'group', text : 'Groups' },
			];

			for( var i=0; i<fields.length; i++ ){
				var value = "";
				var field = fields[i]['field'];

				switch( field ){
					case "roles":
						value = this.post[field];
						break;
					case "group":
						value = this.getGroups().join(", ");
						break;
					default:
						value = this.post[field];
				}

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
