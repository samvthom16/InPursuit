var defaultMixin = require( '../mixins/default.js' );
var memberMixin = require( '../mixins/member.js' );

module.exports = Vue.component( 'inpursuit-member-card', {
	props		: ['post'],
	mixins	: [ defaultMixin, memberMixin ],
	template: "<div class='inpursuit-member-card inpursuit-member-title'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
	 	"<div><h3><router-link :to='getPermalink()'>{{ post.title.rendered }}</router-link></h3>" +
		"<p class='inpursuit-text-muted'>{{ post.member_status }}</p>" +
		"<div v-html='listTermsHTML()'></div>" +
		"<p class=''>Was added {{ post.date | moment }}</p></div></div>",
	methods: {
		getPermalink(){
			return "/members/" + this.post.id;
		},

	}
} );
