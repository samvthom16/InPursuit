var endpoints = require( '../lib/endpoints.js' );

var defaultMixin = require( '../mixins/default.js' );

var API = require( '../lib/api.js' );

module.exports = {
	mixins	: [ defaultMixin ],
	components: { vuejsDatepicker },
	template: "<div class='inpursuit-form' style='margin-top:30px;'>" +

		"<div class='inpursuit-form-field'>" +
		"<label>{{ labels.title }}</label><input v-model='post.title.raw' type='text' />" +
		"</div>" +

		"<div v-if='!hide_post.date' class='inpursuit-form-field'>" +
		"<label>{{ labels.date }}</label><vuejs-datepicker v-model='post.date' />" +
		"</div>" +

		"<div class='inpursuit-grid2'><div class='inpursuit-form-field' v-for='metafield in metafields'>" +
		"<label>{{ metafield.label }}</label><input v-model='post[metafield.field]' type='text' />" +
		"</div></div>" +

		"<div class='inpursuit-grid2'><div class='inpursuit-form-field' v-for='event in getSpecialEvents()'>" +
		"<label>{{ event.label }}</label><vuejs-datepicker v-model='post.special_events[event.field]' />" +
		"</div></div>" +

		"<div v-if='!hide_post.content' class='inpursuit-form-field'>" +
		"<label>{{ labels.content }}</label><textarea rows='5' v-model='post.content.raw'></textarea>" +
		"</div>" +

		"<div class='inpursuit-form-field inpursuit-grid2'>" +
		"<inpursuit-select v-for='dropdown in dropdowns' :field='dropdown.field' :label='dropdown.label' :post='post'></inpursuit-select>" +
		"</div>" +

		"<div class='inpursuit-form-field inpursuit-grid2'>" +
		"<inpursuit-checkbox v-for='multiselect in multiselects' :field='multiselect.field' :label='multiselect.label' :post='post'></inpursuit-checkbox>" +
		"</div>" +

		"<div class='inpursuit-form-field' style='margin-top: 40px;'><p>" +
		"<button class='button' type='button' @click='savePost()'>Save Changes</button>" +
		" or <router-link :to='getPermalink()'>Cancel</router-link>" +
		"<span class='spinner' :class='{active: loading}'></span>" +
		"</p></div>" +

		"</div>",
	data(){
		return {
			hide_post:{},
			//hide_post_content : false,
			post_type					: 'members',
			post				: {
				date_gmt 	: '',
				title 		: { rendered : '', raw: '' },
				content 	: { rendered : '', raw : '' },
				status 		: 'publish',
				special_events: {}
			},
			dropdowns			: [],
			multiselects 	: [],
			metafields		: [],
			post_id				: 0,
			loading				: true,
			labels				: {
				title 		: "Post Title",
				date			: "Post Date",
				content 	: "Post Content",
				wedding		: "Date of Wedding",
				birthday	: "Date of Birth"
			},
		}
	},
	methods: {
		getURL: function(){
			if( this.post_id ){
				return endpoints[this.post_type] + "/" + this.post_id;
			}
			return endpoints[this.post_type];
		},
		getPost	: function(){
			var component = this;
			API.request( {
				url					: this.getURL(),
				params			: { context: 'edit' },
				callbackFn	: function( response ){
					component.post = response.data;

					// STOP THE LOADER
					component.loading = false;
				}
			} );
		},

		savePost: function(){

			var component	= this;

			var newPost = Object.assign( {}, component.post );

			component.loading = true;

			API.request( {
				method	: 'post',
				data 		: newPost,
				url			: this.getURL(),
				callbackFn: function( response ){
					component.loading = false;

					//console.log( response.data );

					component.post_id = response.data.id;

					// REDIRECT TO THE SINGLE PAGE
					component.$router.push( component.getPermalink() );
				}
			} );

		},
		getPermalink	: function(){
			var url = '/' + this.post_type;
			if( this.post_id ){
				url += '/' + this.post_id;
			}
			return url;
		},
		getSpecialEvents: function(){
			var events = [];
			for( var key in this.post.special_events ){
				var event = {
					field: key,
					label: this.labels[key],
				}
				events.push( event );
			}
			return events;
		},
		init: function(){}
	},
	created: function(){
		this.init();

		var post_id = this.$route.params.id;
		if( post_id ){
			this.post_id = post_id;
			this.getPost();
		}
		else{
			this.loading = false;
		}
	}
};
