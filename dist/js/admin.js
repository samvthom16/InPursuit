new Vue({
  el: '#inpursuit-event-members',
  data() {
		return {
			debounce: null,
			searchQuery: '',
			total_selected : 0,
			total : 0,
			posts: [],
			selected_posts: [],
			loading: false,
			show_event_attendants: 0,
			per_page: 21,
			pages: [],
			page: 1
		}
  },
	methods: {
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
			API().request( {
				url			: 'wp/v2/' + this.getMembersPostType() + '/',
				//post_type		: this.getMembersPostType(),
				params	: {
					event_id 							: this.getEventID(),
					search								: this.searchQuery,
					show_event_attendants : this.show_event_attendants,
					page									: this.page,
					per_page							: this.per_page,
					order									: 'asc',
					orderby								: 'title'
				},
				callbackFn	: function( response ){

					//console.log( response.headers['x-wp-total'] );
					//console.log( response.headers['x-wp-totalpages'] );

					component.pages = [];

					for( var i=1; i<=response.headers['x-wp-totalpages']; i++ ){
						component.pages.push( i );
					}

					component.total = response.headers['x-wp-total'];

					component.posts = response.data;
					component.loading = false;

					//component.total = posts.length;

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
		debounceSearch( event ) {
      clearTimeout( this.debounce )
      this.debounce = setTimeout(() => {
        this.searchQuery = event.target.value;
				this.getPosts();
			}, 600)
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

// DASHBOARD ELEMENTS
new Vue( { el: '#inpursuit-latest-members' } );
new Vue( { el: '#inpursuit-latest-events' } );
// DASHBOARD ELEMENTS

new Vue( { el: '#inpursuit-member-history' } );
new Vue({ el: '#inpursuit-member-info' });
