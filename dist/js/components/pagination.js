module.exports = Vue.component( 'inpursuit-page-pagination', {
	props: ['total_pages'],
	template: '<nav aria-label="Page navigation example" v-if="getPages().length > 1">' +
		'<ul class="inpursuit-pagination"><li class="page-item"><button type="button" class="page-link" v-if="$parent.page != 1" @click="$parent.page--"> Previous </button></li>' +
		'<li class="page-item">' +
		'<button type="button" class="page-link" :class="{active: $parent.page === pageNumber}" v-for="pageNumber in getPages()" @click="$parent.page = pageNumber"> {{pageNumber}} </button>' +
		'</li>' +
		'<li class="page-item">' +
		'<button type="button" @click="$parent.page++" v-if="$parent.page < getPages().length" class="page-link"> Next </button>' +
		'</li></ul><p class="inpursuit-text-muted" style="margin-top:0">Showing total of {{ $parent.total }} items</p></nav>',
	methods: {
		getPages: function(){
			var pages = [];
			for( var i=1; i<=this.total_pages; i++ ){
				pages.push( i );
			}
			return pages;
		}
	}
} );
