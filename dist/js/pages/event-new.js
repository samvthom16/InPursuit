var eventEditMixin = require( '../mixins/event-edit.js' );

module.exports = Vue.component( 'inpursuit-event-new', {
	mixins: [ eventEditMixin ],
	methods: {
		init: function(){
			this.post.event_type = '';
			this.post.location = '';
		}
	}
} );
