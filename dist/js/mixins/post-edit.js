var endpoints = require( '../lib/endpoints.js' );

var defaultMixin = require( '../mixins/default.js' );

var API = require( '../lib/api.js' );

module.exports = {
	mixins	: [ defaultMixin ],
	template: "<div class='inpursuit-form' style='margin-top:30px;'>" +

		"<div class='inpursuit-form-field'>" +
		"<label>{{ labels.title }}</label><input v-model='post.title.rendered' type='text' />" +
		"</div>" +

		"<div class='inpursuit-form-field'>" +
		"<label>{{ labels.content }}</label><textarea rows='5' v-model='post.content.rendered'></textarea>" +
		"</div>" +

		"<div class='inpursuit-form-field inpursuit-grid2'>" +
		"<inpursuit-select v-for='dropdown in dropdowns' :field='dropdown.field' :label='dropdown.label' :post='post'></inpursuit-select>" +
		"</div>" +

		"<div class='inpursuit-form-field' style='margin-top: 40px;'><p>" +
		"<button class='button' type='button' @click='savePost()'>Save Changes</button>" +
		" or <router-link :to='getPermalink()'>Cancel</router-link>" +
		"<span class='spinner' :class='{active: loading}'></span>" +
		"</p></div>" +

		"</div>",
	data(){
		return {
			post_type		: 'members',
			post				: {
				title : { rendered : '' },
				content : { rendered : '' },
			},
			dropdowns		: [],
			post_id			: 0,
			loading			: true,
			labels			: {
				title 	: "Post Title",
				content : "Post Content"
			},
		}
	},
	methods: {
		getURL: function(){
			return endpoints[this.post_type] + "/" + this.post_id;
		},
		getPost	: function(){
			var component = this;
			API.request( {
				url					: this.getURL(),
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		savePost: function(){

			var component	= this;
			var newPost = Object.assign( {}, this.post );

			component.loading = true;
			newPost.title = this.post.title.rendered;
			newPost.content = this.post.content.rendered;

			API.request( {
				method	: 'post',
				data 		: newPost,
				url			: this.getURL(),
				callbackFn: function(){
					// REDIRECT TO THE SINGLE PAGE
					component.$router.push( component.getPermalink() );
				}
			} );

		},
		getPermalink	: function(){
			return '/' + this.post_type + '/' + this.post_id;
		}
	},
	created: function(){
		var post_id = this.$route.params.id;
		if( post_id ){
			this.post_id = post_id;
		}
		this.getPost();
	}
};
