var defaultMixin = require( '../mixins/default.js' );
var managerMixin = require( '../mixins/manager.js' );
var paginationMixin = require( '../mixins/pagination.js' );

module.exports = Vue.component( 'template-managers', {
	mixins	: [ defaultMixin, paginationMixin, managerMixin ],
	template: `<div>
			<inpursuit-searchfilters :loading='loading' newLink='/managers/new' newLabel='New Manager'></inpursuit-searchfilters>
			<div class="inpursuit-grid3">
				<inpursuit-manager-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-manager-card>
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
