var memberEditMixin = require( '../mixins/member-edit.js' );

module.exports = Vue.component( 'inpursuit-member-new', {
	mixins	: [ memberEditMixin ],
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
