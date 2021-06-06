var API = function(){

	var self = {
		base_url 	: 'http://localhost/wordpress/wp-json/',
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

	self.getRequest = function( options ){
		//console.log( options );
		axios.get( updateURL( options.url, options.params ) ).then( function( response ){

			if ( typeof options.callbackFn === 'function' ) {
				options.callbackFn( response.data );
			}
		} );

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
				options.callbackFn( response.data );
			}
		} );
	};

	self.getPosts = function( options ){
		var options = getOptions( options );
		options.url = 'wp/v2/' + options.post_type;
		return self.request( options );
	};

	self.getPost = function( options ){
		var options = getOptions( options );

		var params = {};
		params.url = 'wp/v2/' + options.post_type + '?slug=' + options.slug;
		params.callbackFn = function( response ){
			var post;
			if( response.length ){
				post = response[0];
			}
			options.callbackFn( post );
		};

		return self.request( params );
	};

	/*
	self.updatePost = function( post, options ){
		var options = getOptions( options );
		var params = { method: 'post', data: post };
		params.url = 'wp/v2/' + options.post_type + '/' + post.id;
		params.callbackFn = function( response ){
			options.callbackFn( response );
		};
		return self.request( params );
	}
	*/


	self.getAuthor = function( options ){
		var options = getOptions( options );
		options.url = 'wp/v2/users/' + options.id;
		return self.request( options );
	};

	return self;
};

var app = new Vue({
  el: '#inpursuit-event-members',
  data() {
		return {
			debounce: null,
			searchQuery: '',
			total_selected : 0,
			total : 0,
			posts: [],
			selected_posts: []
		}
  },
	methods: {
		getEventID	: function(){
			return document.getElementById( "post_ID" ).value;
		},
		toggleSelect: function (post) {
			post.attended = !post.attended;
			this.savePost( post );
			this.refreshCount();
		},
		totalSelected: function(){
			return this.posts.filter( post => {
				return post.attended;
		 }).length;
		},
		refreshCount: function(){
			this.total_selected = this.totalSelected();
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
			API().getPosts( {
				post_type		: this.getMembersPostType(),
				params	: { event_id : this.getEventID(), search: this.searchQuery },
				callbackFn	: function( posts ){
					component.posts = posts;
					component.total = posts.length;
				}
			} );
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
});
