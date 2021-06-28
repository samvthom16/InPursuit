var debounceMixin = require( '../mixins/debounce.js' );

module.exports = Vue.component( 'inpursuit-search-text', {
	props		: ['searchQuery'],
	mixins	: [debounceMixin],
	template: '<input type="text" name="search" @input="debounceEvent" placeholder="Search" />',
	methods	: {
		debounceCallback: function( event ){
			this.$parent.searchQuery = event.target.value;
			this.$parent.getPosts();
		},
	}
} );
