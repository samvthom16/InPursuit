var defaultMixin = require( '../mixins/default.js' );
var memberMixin = require( '../mixins/member.js' );
var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );

module.exports = Vue.component( 'inpursuit-member', {
	mixins	: [ defaultMixin, memberMixin ],
	template: "<div style='max-width:800px;margin-top:30px;'>" +
		"<p><router-link to='/members'>&#8592;List Of Members</router-link></p>" +
		"<div v-if='post.title' class='inpursuit-document' style='margin-bottom:30px;'>" +
		"<div class='inpursuit-member-title'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
		"<div><h1 v-if='post.title'>{{ post.title.rendered }}</h1>" +
		"<div v-html='subtitleHTML()'></div>" +
		"<div v-html='specialEventsHTML()'></div>" +
		"<div v-html='listTermsHTML()'></div>" +
		"<router-link :to='editLink()'>Edit</router-link>" +
		//"<inpursuit-actions :edit_link='editLink()' delete='1'></inpursuit-actions>" +
		"</div></div>" +
		"</div><timeline :member_id='post_id' per_page='10'></timeline></div>",
	data(){
		return {
			post		: {},
			post_id	: 0
		}
	},
	methods: {
		getPost: function(){
			var component = this;
			API.request( {
				url					: endpoints.members + "/" + this.post_id,
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		editLink: function(){
			return '/members/' + this.post_id + '/edit';
		}
	},
	created: function(){
		var post_id = this.$route.params.id;
		if( post_id ){
			this.post_id = post_id;
		}
		this.getPost();
	}
} );
