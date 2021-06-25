Vue.use( Dropdown );

Vue.component( 'add-comment', {
	template	: "<div><button type='button' class='button' @click='openForm()'>Add Comment</button><div class='thickbox-modal' :class='status'><div class='thickbox-modal-content'><header>Add Comment<button type='button' class='close-btn' @click='closeForm()'>&times;</button></header><p><textarea v-model='comment.comment'></textarea></p><p><button type='button' class='button' @click='saveForm()'>Submit</button><span class='spinner' :class='{active: loading}'></span></p></div></div></div>",
	props			: [ 'comment_id', 'post_id' ],
	data			: function(){
		return {
			loading	: false,
			status	: 'closed',
			comment	: {}
		}
	},
	methods	: {
		getUrl: function(){
			var url = 'inpursuit/v1/comments/';
			if( this.comment_id ){
				url += this.comment_id;
			}
			return url;
		},
		openForm	: function(){ console.log( this.post_id ); this.status = 'open'; },
		closeForm	: function(){ this.status = 'closed'; },
		saveForm	: function(){
			var comment = this.comment;
			comment.post = this.post_id;

			if( !comment.comment ){
				alert( 'Comment cannot be empty!' );
			}
			else{
				this.loading = true;
				var component = this;

				API().request( {
					method	: 'post',
					url			: this.getUrl(),
					data		: comment,
					callbackFn	: function( response ){
						component.loading = false;
						component.status = 'closed';
						component.$parent.refreshPosts();
						component.comment.comment = '';
					}
				} );

			}
		}
	}
} );

Vue.component( 'timeline', {
	props	: ['member_id', 'per_page'],
  template: '<div><add-comment :comment_id="0" :post_id="member_id"></add-comment><div class="inpursuit-timeline" style="margin-top:20px;margin-left: 20px;"><div class="container-right" :class="post.type" v-for="post in posts"><timeline-event :post="post"></timeline-event></div></div><p><span class="spinner" :class="{active: loading}"></span></p><p v-if="page < total_pages"><button type="button" class="button" @click="page++">Load More</button></p></div>',
	data	: function () {
    return {
			posts					: [],
			loading				: false,
			pages					: [],
			page					: 1,
			total_pages		: 0
    }
  },
	methods: {
		getUrl: function(){
			var url = 'inpursuit/v1/history/'
			if( this.member_id ){
				url += this.member_id;
			}
			return url;
		},
		getPosts: function(){
			var component = this;
			this.loading = true;

			API().request( {
				url			: this.getUrl(),
				params	: {
					page			: this.page,
					per_page	: this.per_page
				},
				callbackFn	: function( response ){

					for( var index in response.data ){
						component.posts.push( response.data[ index ] );
					}

					component.total_pages = response.headers['x-wp-totalpages'];
					component.loading = false;

				}
			} );
		},
		refreshPosts: function(){
			this.posts = [];
			this.getPosts();
		}
	},
	created: function(){
		this.getPosts();
	},
	watch: {
		page( current_page ){
			this.page = current_page;
			this.getPosts();
		}
	}
});

Vue.component( 'timeline-event', {
	props	: ['post'],
  template: '<div class="content"><h4>{{post.date | moment }}<span class="spinner" :class="{active: loading}"></span></h4><p>{{ getTitle() }}</p><div class="post-terms"><span class="badge" :class="term.taxonomy" v-for="term in post.terms">{{ term.name }}</span></div><button v-if="post.type == \'comment\'" type="button" @click="deleteItem()" class="button delete-button">Delete</button></div>',
	data	: function () {
    return {
			loading	: false,
		}
  },
	methods: {
		getTitle: function(){
			if( this.post.type == 'comment' ) return this.post.text;
			return this.post.title.rendered;
		},
		deleteItem: function(){
			var component = this;
			if( confirm( "Are you sure you want to delete this?" ) ){
				var url = "inpursuit/v1/comments/" + this.post.id;
				component.loading = true;
				API().request( {
					method	: 'delete',
					url			: url,
					callbackFn	: function( response ){
						component.loading = false;
						component.$parent.refreshPosts();
					}
				} );
			}
		}
	},
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
});

Vue.component( 'special-event', {
	props	: ['title', 'value', 'slug'],
  data	: function () {
    return {
      showFlag: false
    }
  },
  template: '<div><label><input type="checkbox" name="flag" v-model="showFlag" />Add {{ title }}</label><p v-if="showFlag"><input :name="slug" :value="value" type="date" /></p></div>',
	created	: function(){
		if( this.value != 0 ) this.showFlag = true;
	}
});



Vue.component( 'latest-updates', {
	props		: [ 'per_page', 'post_type' ],
  template: '<div><div v-for="post in posts" style="margin-bottom:20px;"><h4 style="margin: 0;"><a :href="post.edit_url">{{ post.title.rendered }}</a></h4><p style="margin: 0;">Was added {{ post.date | moment }} by {{ post.author_name }}</p></div></div>',
	data		: function(){
		return {
			posts		: [],
		}
	},
	methods: {
		getPosts: function(){
			var component = this;

			var url  = 'wp/v2/' + this.post_type;
			if( this.post_type == 'inpursuit-events' ){
				url  = 'inpursuit/v1/history';
			}

			API().request( {
				url			: url,
				params	: {
					per_page: this.per_page
				},
				callbackFn	: function( response ){
					component.posts = response.data;
				}
			} );
		},
	},
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
	created: function(){
		this.getPosts();
	},
} );

var defaultComponent = {
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
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
			settings		: {}
		}
	},
	methods	: {
		resetPagination: function( response ){
			this.total_pages = response.headers['x-wp-totalpages'];
			this.total = response.headers['x-wp-total'];
		},
		getPosts: function(){},
		getDefaultParams: function(){
			return {
				search 		: this.searchQuery,
				page			: this.page,
				per_page	: this.per_page,
				order			: 'asc',
				orderby		: 'title'
			}
		},
		getSettings: function(){
			var component = this;
			API().request( {
				url			: 'inpursuit/v1/settings',
				callbackFn	: function( response ){
					component.settings = response.data;
				}
			} );
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
			filterTerms: {
				gender : {
					slug	: 'gender',
					label	: 'All Gender',
					value	: ''
				},
				member_status : {
					slug	: 'member_status',
					label	: 'All Status',
					value	: ''
				},
				location : {
					slug	: 'location',
					label	: 'All Location',
					value	: ''
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
		getURL: function(){
			return 'wp/v2/inpursuit-members/';
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
	}
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

Vue.component( 'inpursuit-search-text', {
	props		: ['searchQuery'],
	mixins	: [debounceComponent],
	template: '<input type="text" name="search" @input="debounceEvent" placeholder="Search" />',
	methods	: {
		debounceCallback: function( event ){
			this.$parent.searchQuery = event.target.value;
			this.$parent.getPosts();
		},
	}
} );

Vue.component( 'inpursuit-page-pagination', {
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

Vue.component( 'inpursuit-featured-image', {
	props		: ['image_url'],
	template: "<div class='inpursuit-featured-image'><img :src='image_url' /></div>",
} );

Vue.component( 'inpursuit-member-card', {
	props		: ['post'],
	mixins	: [ defaultComponent, memberComponent ],
	template: "<div class='inpursuit-member-card'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
	 	"<div><h3>{{ post.title.rendered }}</h3><p class='inpursuit-text-muted'>{{ genderAgeText(post) }}</p>" +
		"<p class='text-left location-text' v-if='post.location.length > 0'><span class='dashicons dashicons-location'></span>{{ locationText(post) }}</p>" +
		"<p class=''>Was added {{ post.date | moment }}</p></div></div>",
	methods: {

	}
} );

Vue.component( 'inpursuit-dropdown', {
	props		: ['settings', 'placeholder', 'slug'],
	mixins	: [debounceComponent],
	template: '<Dropdown :options="getOptions()" :disabled="false" v-on:selected="debounceEvent" :maxItem="10" :placeholder="placeholder"></Dropdown>',
	methods	: {
		debounceCallback: function( option ){
			if( option.id != undefined ){
				this.$parent.filterTerms[ this.slug ]['value'] = option.id;
				this.$parent.getPosts();
			}
		},
		getOptions: function(){
			var options = [{
				id 	: '0',
				name: this.placeholder
			}];
			var slug = this.slug;
			if( this.settings[slug] != undefined ){
				for( var key in this.settings[slug] ){
					options.push( {
						'id'		: key,
						'name'	: this.settings[slug][key]
					} );
				}
			}
			return options;
		}
	}
} );

Vue.component( 'inpursuit-members-card', {
	mixins	: [ defaultComponent, memberComponent, paginationComponent ],
  template: '<div><p class="inpursuit-search-filters">' +
		'<inpursuit-search-text :searchQuery="searchQuery"></inpursuit-search-text><inpursuit-dropdown v-for="term in filterTerms" :settings="settings" :slug="term.slug" :placeholder="term.label"></inpursuit-dropdown>' +
		'<span class="spinner" :class="{active: loading}"></span></p><div class="inpursuit-grid"><inpursuit-member-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-member-card></div>' +
		'<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination></div>',
	methods: {
		getPosts: function(){
			var component = this,
				params			= component.getDefaultParams();

			component.loading = true;

			params = this.addFilterParams( params );

			API().request( {
				url					: component.getURL(),
				params			: params,
				callbackFn	: function( response ){
					component.resetPagination( response );
					component.posts = response.data;
					component.loading = false;
				}
			} );
		},

	},
} );
