module.exports = Vue.component( 'timeline', {
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
			var url = endpoints.history;
			if( this.member_id ){
				url += '/' + this.member_id;
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
} );
