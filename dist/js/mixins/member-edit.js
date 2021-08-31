var endpoints = require( '../lib/endpoints.js' );

var defaultMixin = require( '../mixins/default.js' );
var postEditMixin = require( '../mixins/post-edit.js' );

var API = require( '../lib/api.js' );

module.exports = {
	mixins	: [ postEditMixin ],
	data(){
		return {
			hide_post:{
				content	: true,
				date		: true
			},
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
};
