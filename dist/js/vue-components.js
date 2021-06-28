Vue.use( Dropdown );
Vue.use( VueRouter );


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
			var url = endpoints.comments;
			if( this.comment_id ){
				url += this.comment_id;
			}
			return url;
		},
		openForm	: function(){ this.status = 'open'; },
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
});

Vue.component( 'timeline-event', {
	props	: ['post'],
	mixins: [defaultComponent],
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
} );

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

			var url  = endpoints.members;
			if( this.post_type == 'inpursuit-events' ){
				url  = endpoints.history;
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

Vue.component( 'inpursuit-event-progress', {
	props		: ['percentage'],
	template: '<div class="participation-wrapper"><div class="single-chart">' +
			'<svg viewBox="0 0 36 36" class="circular-chart blue">' +
			'<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />' +
			'<path class="circle" :stroke-dasharray="stroke()" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />' +
			'<text x="18" y="20.35" class="attending-percentage">{{ html() }}</text>' +
			'</svg></div></div>',
	methods: {
		html: function(){
			return this.percentage + "%";
		},
		stroke: function(){
			return this.percentage + ", 100";
		}
	}
} );

Vue.component( 'inpursuit-member-card', {
	props		: ['post'],
	mixins	: [ defaultComponent, memberComponent ],
	template: "<div class='inpursuit-member-card inpursuit-member-title'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
	 	"<div><h3><router-link :to='getPermalink()'>{{ post.title.rendered }}</router-link></h3><p class='inpursuit-text-muted'>{{ genderAgeText(post) }}</p>" +
		"<p class='inpursuit-location-text' v-if='post.location.length > 0'><span class='dashicons dashicons-location'></span>{{ locationText(post) }}</p>" +
		"<p class=''>Was added {{ post.date | moment }}</p></div></div>",
	methods: {
		getPermalink(){
			return "/members-" + this.post.id;
		}
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
				if( this.$parent.page != undefined ){ this.$parent.page = 1;}
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





Vue.component( 'inpursuit-event-card', {
	props		: ['post'],
	mixins	: [ defaultComponent, memberComponent ],
	template: "<div class='inpursuit-member-card inpursuit-event-title'><inpursuit-event-progress :percentage='post.attendants_percentage'></inpursuit-event-progress>" +
	 	"<div><h3><router-link :to='getPermalink()'>{{ post.title.rendered }}</router-link></h3><p class='inpursuit-text-muted'>{{ genderAgeText(post) }}</p>" +
		"<p class='inpursuit-location-text' v-if='post.location.length > 0'><span class='dashicons dashicons-location'></span>{{ locationText(post) }}</p>" +
		"<p class=''>Was added {{ post.date | moment }}</p></div></div>",
	methods: {
		getPermalink(){
			return "/events-" + this.post.id;
		}
	}
} );


Vue.component( 'inpursuit-actions', {
	props: [ 'edit_link' ],
	template: "<ul><li><router-link :to='edit_link'>Edit</router-link></li><li></li></ul>",
	methods	: {

	}
} );

var memberLayout = Vue.component( 'inpursuit-member', {
	mixins	: [ defaultComponent, memberComponent ],
	template: "<div style='max-width:800px;margin-top:30px;'>" +
		"<p><router-link to='/members'>&#8592;List Of Members</router-link></p>" +
		"<div class='inpursuit-document' style='margin-bottom:30px;'>" +
		"<div class='inpursuit-member-title'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
		"<div><h1 v-if='post.title'>{{ post.title.rendered }}</h1><p class='inpursuit-text-muted'>{{ genderAgeText(post) }}</p>" +
		"<p class='inpursuit-location-text' v-if='post.location && post.location.length'><span class='dashicons dashicons-location'></span>{{ locationText(post) }}</p>" +
		"<inpursuit-actions :edit_link='editLink()' delete='1'></inpursuit-actions>" +
		"</div></div>" +
		"</div><timeline :member_id='post_id' per_page='10'></timeline></div>",
	data(){
		return {
			post		: {},
			post_id	: 0
		}
	},
	methods: {
		getPost: function(){
			var component = this;
			API().request( {
				url					: endpoints.members + "/" + this.post_id,
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		editLink: function(){
			return 'members-' + this.post_id + '/edit';
		}
	},
	created: function(){
		var post_id = this.$route.params.id;
		if( post_id ){
			this.post_id = post_id;
		}
		this.getPost();
	}
} );

var memberEditLayout = Vue.component( 'inpursuit-member-edit', {
	mixins	: [ defaultComponent, memberComponent ],
	template: "<div style='max-width:800px;margin: 0 auto;'>" +
		"<div class='inpursuit-document' style='margin-bottom:30px;'>" +
		"<div class='inpursuit-member-title'><inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>" +
		"<div><p v-if='post.title'><input type='text' v-model='post.title.rendered' placeholder='First & Last Name' /></p>" +
		"<inpursuit-dropdown v-for='term in filterTerms' :settings='settings' :slug='term.slug' :placeholder='term.label'></inpursuit-dropdown>" +
		"</div></div><p><button class='button' type='button'>Save Changes</button></p>" +
		"</div></div>",
	data(){
		return {
			post		: {},
			post_id	: 0
		}
	},
	methods: {
		getPost: function(){
			var component = this;
			API().request( {
				url					: endpoints.members + "/" + this.post_id,
				callbackFn	: function( response ){
					component.post = response.data;
					component.loading = false;
				}
			} );
		},
		editLink: function(){
			return 'members-' + this.post_id + '/edit';
		}
	},
	created: function(){
		var post_id = this.$route.params.id;
		if( post_id ){
			this.post_id = post_id;
		}
		this.getPost();
		this.getSettings();
	}
} );



var TEMPLATES = function(){
	var self = this;

	self.home = Vue.component( 'home', {
		template: "<div>Hello World</div>",
		created: function(){
			this.$router.push( '/dashboard' );
		}
	} );

	self.dashboard = Vue.component( 'template-dashboard', {
		template: "<div class='inpursuit-grid3' style='margin-top: 30px;'>" +
		"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Recent Members</h4><latest-updates per_page='5' post_type='inpursuit-members'></latest-updates></div>" +
		"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Recent Events</h4><latest-updates per_page='5' post_type='inpursuit-events'></latest-updates></div>" +
		"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Demographic</h4><inpursuit-choropleth-map></inpursuit-choropleth-map></div>"	+
		"</div>",

	} );

	self.members = Vue.component( 'template-members', {
		mixins	: [ defaultComponent, paginationComponent, memberComponent ],
	  template: '<div><p class="inpursuit-search-filters">' +
			'<inpursuit-search-text :searchQuery="searchQuery"></inpursuit-search-text>' +
			'<inpursuit-dropdown v-for="term in filterTerms" :key="term.slug" :settings="settings" :slug="term.slug" :placeholder="term.label"></inpursuit-dropdown>' +
			'<span class="spinner" :class="{active: loading}"></span></p><div class="inpursuit-grid"><inpursuit-member-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-member-card></div>' +
			'<p v-if="posts.length < 1">No information was found.</p>' +
			'<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination></div>',
		data(){
			return {
				per_page: 9
			}
		},
	} );

	self.events = Vue.component( 'template-events', {
		mixins	: [ defaultComponent, paginationComponent, eventComponent ],
	  template: '<div><p class="inpursuit-search-filters">' +
			'<inpursuit-search-text :searchQuery="searchQuery"></inpursuit-search-text>' +
			'<inpursuit-dropdown v-for="term in filterTerms" :settings="settings" :slug="term.slug" :placeholder="term.label"></inpursuit-dropdown>' +
			'<span class="spinner" :class="{active: loading}"></span></p>' +
			'<div class="inpursuit-grid"><inpursuit-event-card :key="post.id" :post="post" v-for="post in posts"></inpursuit-event-card></div>' +
			'<p v-if="posts.length < 1">No information was found.</p>' +
			'<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination></div>',
		data(){
			return {
				per_page	: 9,
				orderby		: 'date',
				order			: 'desc'
			}
		}
	} );

	return self;
};

var templates = TEMPLATES();


var routes = [
	{
		path			: '/',
		component	: templates.home
	},
	{
		path			: '/dashboard',
		component	: templates.dashboard
	},
	{
		path			: '/members',
		component	: templates.members
	},
	{
		path			: '/events',
		component	: templates.events
	},
	{
		path			: '/members-:id',
		component	: memberLayout
	},
	{
		path			: '/members-:id/edit',
		component	: memberEditLayout
	},
	/*
	{
		path: '/blog/:slug+',
		//component: templates.single_post
	},
	*/
];

var router = new VueRouter( { routes } );
