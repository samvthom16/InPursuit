var defaultMixin = require( '../mixins/default.js' );
var memberMixin = require( '../mixins/member.js' );

module.exports = Vue.component( 'inpursuit-member-card', {
	props		: ['post'],
	mixins	: [ defaultMixin, memberMixin ],
	template: "<div class='inpursuit-member-card inpursuit-member-title'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
	 	"<div><h3><a :href='post.edit_url'>{{ post.title.rendered }}</a></h3><p class='inpursuit-text-muted'>{{ genderAgeText(post) }}</p>" +
		"<p class='inpursuit-location-text' v-if='post.location.length > 0'><span class='dashicons dashicons-location'></span>{{ locationText(post) }}</p>" +
		"<p class=''>Was added {{ post.date | moment }}</p></div></div>",
	methods: {
		getPermalink(){
			return "/members-" + this.post.id;
		}
	}
} );
