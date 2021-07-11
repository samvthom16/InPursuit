var defaultMixin = require( '../mixins/default.js' );
var memberMixin = require( '../mixins/member.js' );
var paginationMixin = require( '../mixins/pagination.js' );

module.exports = Vue.component( 'template-members', {
	mixins	: [ defaultMixin, paginationMixin, memberMixin ],
	template: `<div>
			<inpursuit-searchfilters :searchQuery='searchQuery' :filterTerms='filterTerms' :loading='loading' newLink='/members/new' newLabel='New Member'></inpursuit-searchfilters>
			<div class="inpursuit-grid3">
				<inpursuit-member-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-member-card>
			</div>
			<p v-if="posts.length < 1">No information was found.</p>
			<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination>
		</div>`,
	data(){
		return {
			per_page: 9,
		}
	},
} );
