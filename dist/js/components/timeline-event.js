var defaultMixin = require( '../mixins/default.js' );
var API = require( '../lib/api.js' );

module.exports = Vue.component( 'timeline-event', {
	props	: ['post'],
	mixins: [defaultMixin],
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
				API.request( {
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
