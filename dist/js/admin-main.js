//console.log( inpursuitSettings );

var API = function(){

	var self = {
		base_url 	: inpursuitSettings.root,
	};

	function getOptions( options ){
		return Vue.util.extend( {
			method			: 'get',
			post_type		: 'posts',
			id					: '',
			slug 				: '',
			url  				: '',
			callbackFn	: function(){}
		}, options );
	}

	function updateURL( url, params ){
		var i = 0;
		for ( var key in params ) {
			if( i == 0 ) url += "?";
			else url += "&";
			url += key + "=" + params[key];
			i++;
		}
		return url;
	}

	self.request = function( options ){
		var url = self.base_url + options.url;
		if( options.params != undefined ){
			url = updateURL( url, options.params );
		}

		var api_obj;
		if( options.method == 'post' ){
			console.log( url );
			api_obj = axios.post( url, options.data, {
				headers: {
					'X-WP-Nonce': inpursuitSettings.nonce
				}
			} );
		}
		else{
			api_obj = axios.get( url );
		}

		api_obj.then( function( response ){
			//console.log( response );
			if ( typeof options.callbackFn === 'function' ) {
				options.callbackFn( response );
			}
		} );
	};

	return self;
};

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
			per_page: 20,
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
					per_page							: this.per_page
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

new Vue( { el: '#inpursuit-member-history' } );
new Vue({ el: '#inpursuit-member-info' });
new Vue( { el: '#inpursuit-timeline-history' } );
