Vue.use( Dropdown );
Vue.use( VueRouter );

Vue.component( 'inpursuit-actions', {
	props: [ 'edit_link' ],
	template: "<ul><li><router-link :to='edit_link'>Edit</router-link></li><li></li></ul>",
	methods	: {

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
		path			: '/members/:id',
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
