var defaultMixin = require( '../mixins/default.js' );
var eventMixin = require( '../mixins/event.js' );
var paginationMixin = require( '../mixins/pagination.js' );

module.exports = Vue.component( 'template-events', {
	mixins	: [ defaultMixin, paginationMixin, eventMixin ],
	template: '<div><p class="inpursuit-search-filters">' +
		'<inpursuit-search-text :searchQuery="searchQuery"></inpursuit-search-text>' +
		'<inpursuit-dropdown v-for="term in filterTerms" :settings="settings" :slug="term.slug" :placeholder="term.label"></inpursuit-dropdown>' +
		'<span class="spinner" :class="{active: loading}"></span></p>' +
		'<div class="inpursuit-grid3"><inpursuit-event-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-event-card></div>' +
		'<p v-if="posts.length < 1">No information was found.</p>' +
		'<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination></div>',
	data(){
		return {
			per_page	: 9,
			orderby		: 'date',
			order			: 'desc'
		}
	}
} );
