var endpoints = require( '../lib/endpoints.js' );
var userEditMixin = require( '../mixins/user-edit.js' );

var API = require( '../lib/api.js' );

module.exports = {
	mixins	: [ userEditMixin ],
	data(){
		return {
			show_element	: {
				update: true,
				multiselect: true
			},
			multiselects : [
				{ label: 'Limit User Access', field: 'group' }
			],
		}
	},

};
