var defaultMixin = require( '../mixins/default.js' );
var memberMixin = require( '../mixins/member.js' );

module.exports = Vue.component( 'inpursuit-member-card', {
	props		: ['post', 'settings'],
	mixins	: [ defaultMixin, memberMixin ],
	template: "<div class='inpursuit-member-card inpursuit-member-title' :class='post.status'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
	 	"<div><h3><router-link :to='getPostLink( post )'>{{ post.title.rendered }}</router-link></h3>" +
		"<div v-html='subtitleHTML()'></div>" +
		"<div v-html='listTermsHTML()'></div>" +
		"<p class=''>Was added {{ post.date | moment }}</p></div></div>",
	methods: {

	}
} );
