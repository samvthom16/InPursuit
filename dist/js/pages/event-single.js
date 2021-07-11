var defaultMixin = require( '../mixins/default.js' );
var eventMixin = require( '../mixins/event.js' );
var singlePostMixin = require( '../mixins/single-post.js' );

module.exports = Vue.component( 'inpursuit-event-single', {
	mixins	: [ defaultMixin, eventMixin, singlePostMixin ],
	template: `<div style='max-width:1000px;margin-top:30px;'>
			<p><router-link to='/events'>&#8592;List Of Events</router-link></p>
			<div v-if='post.status == "draft"' class='inpursuit-post-archived'>This item has been archived.</div>
			<div v-if='post.title' class='inpursuit-document' style='margin-bottom:30px;'>
				<div class='inpursuit-event-title' style='position: relative;'>
					<inpursuit-event-progress :percentage='post.attendants_percentage'></inpursuit-event-progress>
					<div>
						<h1 v-if='post.title'>{{ post.title.rendered }}</h1>
						<div v-if='post.content' class='inpursuit-text-muted' v-html='post.content.rendered'></div>
						<div v-html='listTermsHTML()'></div>
						<inpursuit-post-actions :post='post' :actionCallback='actionCallback'></inpursuit-post-actions>
					</div>
				</div>
			</div>
			<select-members :event_id='post_id'></select-members>
		</div>`,
	data(){
		return {
			post_type : 'events'
		}
	},
} );
