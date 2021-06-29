var defaultMixin = require( '../mixins/default.js' );
var eventMixin = require( '../mixins/event.js' );

module.exports = Vue.component( 'inpursuit-event-card', {
	props		: ['post'],
	mixins	: [ defaultMixin, eventMixin ],
	template: "<div class='inpursuit-member-card inpursuit-event-title'><inpursuit-event-progress :percentage='post.attendants_percentage'></inpursuit-event-progress>" +
	 	"<div><h3><a :href='post.edit_url'>{{ post.title.rendered }}</a></h3><p class='inpursuit-text-muted'>Was added {{ post.date | moment }}</p>" +
		"<div v-html='listTermsHTML()'></div>" +
		"</div></div>",
	methods: {
		getPermalink(){
			return "/events/" + this.post.id;
		}
	}
} );
