var endpoints = require( '../lib/endpoints.js' );
var API = require( '../lib/api.js' );

module.exports = {
	data	: function(){
		return {
			posts				: [],
			total 			: 0,
			total_pages	: 0,
			page				: 1,
			loading			: false,
			searchQuery	: "",
			per_page		: 6,
			url					: '',
			order				: 'asc',
			orderby			: 'title'
		}
	},
	methods	: {
		resetPagination: function( response ){
			this.total_pages = response.headers['x-wp-totalpages'];
			this.total = response.headers['x-wp-total'];
		},
		getPosts: function(){
			var component = this,
				params			= component.getDefaultParams();

			// SWITCH ON THE LOADER
			component.loading = true;

			// ADD PARAMS FROM THE SELECTED FILTERS
			params = this.addFilterParams( params );

			// MAKE THE API REQUEST
			API.request( {
				url					: this.url,
				params			: params,
				callbackFn	: function( response ){

					// RESET THE PAGINATION
					component.resetPagination( response );

					// RESET THE POSTS DATA
					component.posts = response.data;

					// SWITCH OFF THE LOADER
					component.loading = false;
				}
			} );
		},
		getDefaultParams: function(){
			return {
				search 		: this.searchQuery,
				page			: this.page,
				per_page	: this.per_page,
				order			: this.order,
				orderby		: this.orderby
			}
		},
		// ADD TAXONOMY FILTERS TO PARAMS
		addFilterParams: function( params ){
			for( var slug in this.filterTerms ){
				var term_id = this.filterTerms[slug].value;
				if( term_id != undefined ){
					params[ slug ] = term_id;
				}
			}
			return params;
		}
	},
	watch: {
		page( current_page ){
			this.getPosts();
		}
	},
	created: function(){
		this.getPosts();
		this.getSettings();
	},
};
