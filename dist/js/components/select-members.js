var defaultMixin 	= require( '../mixins/default.js' ),
	memberMixin		 	= require( '../mixins/member.js' ),
	paginationMixin	= require( '../mixins/pagination.js' ),
	endpoints				= require( '../lib/endpoints.js' ),
	API							= require( '../lib/api.js' );


Vue.component( 'select-member', {
	props			: ['post'],
	mixins		: [ defaultMixin, memberMixin ],
	template	: '<div><div class="post-row">' +
			'<div class="post-item-toggle" @click="$parent.toggleSelect(post)"><span class="slider round"></span></div>' +
			'<div class="post-content"><h3><router-link :to="getPermalink()">{{ post.title.rendered }}</router-link></h3>' +
			'<div v-html="subtitleHTML()"></div>' +
			'</div></div><div v-html="listTermsHTML()"></div></div>',
	methods: {
		getPermalink: function(){
			return '/members/' + this.post.id;
		}
	}
} );

module.exports = Vue.component( 'select-members', {
	props			: ['event_id'],
	template	: `
		<div>
			<inpursuit-searchfilters :searchQuery='searchQuery' :filterTerms='filterTerms' :loading='loading'></inpursuit-searchfilters>
			<ul class='posts-list inpursuit-grid3'>
				<li class='inpursuit-select-member' :class='{selected: post.attended}' v-for='post in posts'>
					<select-member :post='post'></select-member>
				</li>
			</ul>
			<inpursuit-page-pagination :total_pages='total_pages'></inpursuit-page-pagination>
		</div>`,
	mixins		: [ defaultMixin, memberMixin, paginationMixin ],
	data() {
		return {
			total_selected : 0,
			selected_posts: [],
			per_page : 9,
			show_event_attendants: 0,
		}
  },
	methods: {
		terms : function( post ){

			var terms = [],
				taxonomies = [ 'status', 'group', 'location' ]

			for( var index in taxonomies ){
				if( post[ taxonomies[index] ].length ){
					terms.push({
						name		: post[ taxonomies[index] ],
						taxonomy: taxonomies[index]
					});
				}
			}

			return terms;
		},
		getEventID	: function(){
			return this.event_id;
		},
		toggleSelect: function (post) {
			post.attended = !post.attended;
			this.savePost( post );
		},
		getMembersPostType: function(){
			return 'inpursuit-members';
		},
		savePost: function( post ){
			var url = endpoints.members + '/' + post.id + '?event_id=' + this.getEventID();
			var component = this;
			API.request( {
				method	: 'post',
				data 		: post,
				url			: url,
				callbackFn: function( response ){
					component.$parent.getPost();
				}
			} );
		},
		getPosts: function(){
			var component = this;
			this.loading = true;

			var params = component.getDefaultParams();
			params.event_id = this.getEventID();
			params.show_event_attendants = this.show_event_attendants;

			params = this.addFilterParams( params );

			API.request( {
				url					: endpoints.members,
				params			: params,
				callbackFn	: function( response ){
					component.resetPagination( response );
					component.posts = response.data;
					component.loading = false;
				}
			} );
		},

		refreshPosts( event ){
			if( event.target.checked ){ this.show_event_attendants = 1; }
			else{ this.show_event_attendants = 0; }
			this.getPosts();
		},
	},
} );
