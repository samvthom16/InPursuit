var endpoints = require( '../lib/endpoints.js' );
var userEditMixin = require( '../mixins/user-edit.js' );
var API = require( '../lib/api.js' );

module.exports = {
	mixins	: [ userEditMixin ],
	data(){
		return {
			metafields : [
				{	name: 'email', label: "Email Address" },
				{	name: 'password', label: "Password" },
				{	name: 'first_name', label: "First Name" },
				{	name: 'last_name', label: "Last Name" },
			],
			show_element	: {
				save: true,
				multiselect: true
			},
			multiselects : [
				{ label: 'Limit User Access', field: 'group' }
			],
		}
	},
	methods: {
		init: function(){
			this.post.group = [];
		}
	}
};
