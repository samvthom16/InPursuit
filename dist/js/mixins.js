var endpoints = {
	'members' 	: 'wp/v2/inpursuit-members',
	'events' 		:	'wp/v2/inpursuit-events',
	'settings'	: 'inpursuit/v1/settings',
	'history'		: 'inpursuit/v1/history',
	'comments'	: 'inpursuit/v1/comments',
	'map'				: 'inpursuit/v1/map',
	'regions'		: 'inpursuit/v1/regions',
};

var defaultComponent = {
	data	: function(){
		return {
			settings		: {}
		}
	},
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
	methods: {
		getSettings: function(){
			var component = this;
			API().request( {
				url					: endpoints.settings,
				callbackFn	: function( response ){
					component.settings = response.data;
				}
			} );
		}
	}
};

var paginationComponent = {
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
			API().request( {
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


var memberComponent = {
	data(){
		return {
			url					: endpoints.members,
			filterTerms	: {
				gender : {
					slug	: 'gender',
					label	: 'All Gender',
				},
				member_status : {
					slug	: 'member_status',
					label	: 'All Status',
				},
				location : {
					slug	: 'location',
					label	: 'All Locations',
				}
			},
		}
	},
	methods: {
		genderAgeText: function( post ){
			var gender 	= post['gender'] != null ? post['gender'] : "",
				age 			= post['age'] != null ? post['age'] : "",
				meta 			= [],
				subtitle 	= '';

			if( gender.length ) meta.push( gender );
			if( age.length ) meta.push( age + ' Years' );

			if( meta.length ) subtitle = meta.join( ', ' );
			return subtitle;
		},
		locationText: function( post ){
			return post.location.join( ', ' );
		},
	}
};

var eventComponent = {
	data(){
		return {
			url					: endpoints.events,
			filterTerms	: {
				event_type : {
					slug	: 'event_type',
					label	: 'All Event Types',
				},
				location : {
					slug	: 'location',
					label	: 'All Locations',
				}
			},
		}
	},
};

var debounceComponent = {
	data(){
		return {
			debounce: null,
		}
	},
	methods: {
		debounceCallback: function( event ){},
		debounceEvent		: function( event ) {
			clearTimeout( this.debounce );
      this.debounce = setTimeout(() => {
				this.debounceCallback( event );
			}, 600);
    }
	}
};
