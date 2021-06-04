var API = function(){

	var self = {
		base_url 	: 'http://localhost/wordpress/wp-json/',
	};

	function getOptions( options ){
		return Vue.util.extend( {
			post_type		: 'posts',
			id					: '',
			slug 				: '',
			url  				: '',
			callbackFn	: function(){}
		}, options );
	}

	self.request = function( options ){
		var url = self.base_url + options.url;
		axios.get( url ).then( function( response ){
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

	self.getAuthor = function( options ){
		var options = getOptions( options );
		options.url = 'wp/v2/users/' + options.id;
		return self.request( options );
	};

	return self;
};

var app = new Vue({
  el: '#inpursuit-event-members',
  data: {
		total_selected : 0,
		total : 0,
		posts: []
  },
	methods: {
		toggleSelect: function (post) {
			post.selected = !post.selected;
			this.savePost( post );
			this.refreshCount();
		},
		totalSelected: function(){
			return this.posts.filter( post => {
				return post.selected;
		 }).length;
		},
		refreshCount: function(){
			this.total_selected = this.totalSelected();
		},
		savePost: function( post ){
			
		},
		getPosts: function(){
			var component = this;
			API().getPosts( {
				post_type	: 'inpursuit-members',
				callbackFn	: function( posts ){
					component.posts = posts;
					component.total = posts.length;
				}
			} );
		}
	},
	created: function(){
		this.getPosts();
	}
});
