var endpoints = require( '../lib/endpoints.js' );

var defaultMixin = require( '../mixins/default.js' );

var API = require( '../lib/api.js' );

module.exports = Vue.component( 'latest-updates', {
	props		: [ 'per_page', 'post_type' ],
	mixins	: [ defaultMixin ],
  template: '<div><div v-for="post in posts" style="margin-bottom:20px;"><h4 style="margin: 0;"><a :href="post.edit_url">{{ post.title.rendered }}</a></h4><p style="margin: 0;">Was added {{ post.date | moment }} by {{ post.author_name }}</p></div></div>',
	data		: function(){
		return {
			posts		: [],
		}
	},
	methods: {
		getPosts: function(){
			var component = this;

			var url  = endpoints.members;
			if( this.post_type == 'inpursuit-events' ){
				url  = endpoints.history;
			}

			API.request( {
				url			: url,
				params	: { per_page: this.per_page },
				callbackFn	: function( response ){
					component.posts = response.data;
				}
			} );
		},
	},
	/*
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
	*/
	created: function(){
		this.getPosts();
	},
} );
