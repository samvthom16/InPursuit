var defaultMixin = require( '../mixins/default.js' );
var managerMixin = require( '../mixins/manager.js' );

module.exports = Vue.component( 'inpursuit-manager-card', {
	props		: ['post', 'settings'],
	mixins	: [ defaultMixin, managerMixin ],
	template: "<div class='inpursuit-member-card inpursuit-manager-card inpursuit-member-title'><inpursuit-featured-image :image_url='post.avatar_urls[48]'></inpursuit-featured-image>" +
	 	"<div><h3>{{ post.name }}</h3>" +
	 	"<p class=''>Was registered {{ post.registered_date | moment }}</p></div></div>",
	methods: {

	}
} );
