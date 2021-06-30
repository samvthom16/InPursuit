var defaultMixin = require( '../mixins/default.js' );
var eventMixin = require( '../mixins/event.js' );
var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );

module.exports = Vue.component( 'inpursuit-member', {
	mixins	: [ defaultMixin, eventMixin ],
	template: "<div style='max-width:1000px;margin-top:30px;'>" +
		"<p><router-link to='/events'>&#8592;List Of Events</router-link></p>" +
		"<div v-if='post.title' class='inpursuit-document' style='margin-bottom:30px;'>" +
		"<div class='inpursuit-event-title'><inpursuit-event-progress v-if='post.attendants_percentage' :percentage='post.attendants_percentage'></inpursuit-event-progress>" +
		"<div><h1 v-if='post.title'>{{ post.title.rendered }}</h1><div v-if='post.content' class='inpursuit-text-muted' v-html='post.content.rendered'></div>" +
		"<div v-html='listTermsHTML()'></div>" +
		"<router-link :to='editLink()'>Edit</router-link>" +
		//"<inpursuit-actions :edit_link='editLink()' delete='1'></inpursuit-actions>" +
		"</div></div>" +
		"</div><select-members :event_id='post_id'></select-members></div>",
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
				url					: endpoints.events + "/" + this.post_id,
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		editLink: function(){
			return '/events/' + this.post_id + '/edit';
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
