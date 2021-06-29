var defaultMixin = require( '../mixins/default.js' );
var postEditMixin = require( '../mixins/post-edit.js' );
var API = require( '../lib/api.js' );
var endpoints = require( '../lib/endpoints.js' );


module.exports = Vue.component( 'inpursuit-member-edit', {
	mixins	: [ defaultMixin, postEditMixin ],
	data(){
		return {
			dropdowns	: [
				{ label: 'Gender', field: 'gender' },
				{ label: 'Status', field: 'member_status' },
				{ label: 'Group', field: 'group' },
				{ label: 'Location', field: 'location' }
			],
			labels : {
				title: "Full Name",
				content: "Description"
			}
		}
	},
} );
