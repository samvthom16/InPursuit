Vue.component( 'timeline', {
	props	: ['member_id', 'per_page'],
  template: '<div><div class="inpursuit-timeline" style="margin-top:20px;margin-left: 20px;"><div class="container-right" v-for="post in posts"><timeline-event :post="post"></timeline-event></div></div><p><span class="spinner" :class="{active: loading}"></span></p><p v-if="page < total_pages"><button type="button" class="button" @click="page++">Load More</button></p></div>',
	data	: function () {
    return {
			posts					: [],
			loading				: false,
			//per_page			: 10,
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
  template: '<div class="content"><h4>{{post.date | moment }}</h4><p>{{ post.title.rendered }}</p><div class="post-terms"><span class="badge" :class="term.taxonomy" v-for="term in post.terms">{{ term.name }}</span></div></div>',
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

			API().request( {
				url			: 'wp/v2/' + this.post_type,
				params	: {
					per_page: this.per_page
				},
				callbackFn	: function( response ){
					//component.total = response.headers['x-wp-total'];
					component.posts = response.data;
					//console.log( component.posts );
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
});
