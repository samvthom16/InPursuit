var defaultMixin = require( '../mixins/default.js' );
var eventMixin = require( '../mixins/event.js' );
var paginationMixin = require( '../mixins/pagination.js' );

module.exports = Vue.component( 'template-events', {
	mixins	: [ defaultMixin, paginationMixin, eventMixin ],
	template: `<div>
			<inpursuit-searchfilters :searchQuery='searchQuery' :filterTerms='filterTerms' :loading='loading' newLink='/events/new' newLabel='New Event'></inpursuit-searchfilters>
			<div class="inpursuit-grid3">
				<inpursuit-event-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-event-card>
			</div>
			<p v-if="posts.length < 1">No information was found.</p>
			<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination>
		</div>`,
	data(){
		return {
			per_page	: 9,
			orderby		: 'date',
			order			: 'desc'
		}
	}
} );
