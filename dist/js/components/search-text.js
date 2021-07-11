var debounceMixin = require( '../mixins/debounce.js' );

Vue.component( 'inpursuit-search-text', {
	props		: ['searchQuery', 'debounceCallback'],
	mixins	: [debounceMixin],
	template: '<input type="text" name="search" @input="debounceEvent" placeholder="Search" />',
} );

module.exports = Vue.component( 'inpursuit-searchfilters', {
	props: ['searchQuery', 'filterTerms', 'loading', 'newLink', 'newLabel'],
	template: `<p class="inpursuit-search-filters">
			<inpursuit-search-text :searchQuery="searchQuery" :debounceCallback="debounceCallbackForSearch"></inpursuit-search-text>
			<inpursuit-dropdown v-for="term in filterTerms" :key="term.slug" :slug="term.slug" :placeholder="term.label" :selectCallback='dropdownSelectedCallback'></inpursuit-dropdown>
			<span class="spinner" :class="{active: loading}"></span>
			<router-link class="button" style="float:right;" :to="newLink">{{ newLabel }}</router-link>
		</p>`,
	methods: {
		debounceCallbackForSearch: function( event ){
			this.$parent.searchQuery = event.target.value;
			this.$parent.getPosts();
		},
		dropdownSelectedCallback: function( slug, term_id ){
			this.$parent.filterTerms[ slug ]['value'] = term_id;
			if( this.$parent.page != undefined ){ this.$parent.page = 1;}
			this.$parent.getPosts();
		}
	}
} );
