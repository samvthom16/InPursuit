new Vue({
  el: '#inpursuit-event-members',
	mixins	: [ defaultComponent, memberComponent, paginationComponent ],
  data() {
		return {
			total_selected : 0,
			selected_posts: [],
			per_page : 6,
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
			return document.getElementById( "post_ID" ).value;
		},

		toggleSelect: function (post) {
			post.attended = !post.attended;
			this.savePost( post );
		},

		getMembersPostType: function(){
			return 'inpursuit-members';
		},
		savePost: function( post ){
			API().request( {
				method	: 'post',
				data 		: post,
				url			: 'wp/v2/' + this.getMembersPostType() + '/' + post.id + '?event_id=' + this.getEventID(),
			} );
		},
		getPosts: function(){
			var component = this;
			this.loading = true;

			var params = component.getDefaultParams();
			params.event_id = this.getEventID();
			params.show_event_attendants = this.show_event_attendants;

			params = this.addFilterParams( params );

			API().request( {
				url			: component.getURL(),
				params	: params,
				callbackFn	: function( response ){

					component.resetPagination( response );

					component.posts = response.data;
					component.loading = false;

				}
			} );
		},

		refreshPosts( event ){
			if( event.target.checked ){
				this.show_event_attendants = 1;
			}
			else{
				this.show_event_attendants = 0;
			}
			this.getPosts();
		},
	},
} );

// DASHBOARD ELEMENTS
new Vue( { el: '#inpursuit-latest-members', router: router } );
new Vue( { el: '#inpursuit-latest-events' } );
// DASHBOARD ELEMENTS

new Vue( { el: '#inpursuit-member-history' } );
new Vue({ el: '#inpursuit-member-info' });
