var defaultMixin = require( '../mixins/default.js' );
var postEditMixin = require( '../mixins/post-edit.js' );
var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );


module.exports = Vue.component( 'inpursuit-member-edit', {
	mixins	: [ defaultMixin, postEditMixin ],
	data(){
		return {
			hide_post_content : true,
			dropdowns	: [
				{ label: 'Gender', field: 'gender' },
				{ label: 'Status', field: 'member_status' },
				{ label: 'Location', field: 'location' }
			],
			multiselects : [
				{ field: 'profession', label: 'Choose Profession' },
				{ label: 'Group', field: 'group' },
			],
			metafields		: [
				{ field: 'email', label: 'Email Address' },
				{ field: 'phone', label: 'Phone Number' },
			],
			labels : {
				title: "Full Name",
				content: "Description"
			}
		}
	},
	methods: {
		init: function(){
			this.post.gender = '';
			this.post.member_status = '';
			this.post.location = '';
			this.post.profession = [];
			this.post.group = [];
		}
	}
} );
