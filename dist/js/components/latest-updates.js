var endpoints = require( '../lib/endpoints.js' );

var defaultMixin = require( '../mixins/default.js' );

var API = require( '../lib/api.js' );

module.exports = Vue.component( 'latest-updates', {
	props		: [ 'per_page', 'post_type' ],
	mixins	: [ defaultMixin ],
  template: `
		<div>
			<span class="inpursuit-spinner spinner" :class="{active: loading}"></span>
			<div v-for="post in posts" style="margin-bottom:20px;">
				<h4 style="margin: 0;"><router-link :to="getPermalink( post )">{{ post.title.rendered }}</router-link></h4>
				<p style="margin: 0;">Was added {{ post.date | moment }} by {{ post.author_name }}</p>
			</div>
		</div>`,
	data		: function(){
		return {
			posts		: [],
			loading	: true,
		}
	},
	methods: {
		getPosts: function(){
			var component = this;
			API.request( {
				url			: endpoints[ this.post_type ],
				params	: { per_page: this.per_page },
				callbackFn	: function( response ){
					component.posts = response.data;
					component.loading = false;
				}
			} );
		},
		getPermalink: function( post ){

			var route = {
				name		: "",
				params 	: {}
			};

			if( post.type != undefined && post.type == 'comment' ){
				route.name = "SingleMember";
				route.params = { id : post.post_id };
			}
			else{
				// FOR EVENTS AND MEMBERS 
				route = this.getPostLink( post );
			}

			return route;
		}
	},
	created: function(){
		this.getPosts();
	},
} );
