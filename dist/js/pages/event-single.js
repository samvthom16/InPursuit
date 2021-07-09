var defaultMixin = require( '../mixins/default.js' );
var eventMixin = require( '../mixins/event.js' );
var singlePostMixin = require( '../mixins/single-post.js' );

module.exports = Vue.component( 'inpursuit-member', {
	mixins	: [ defaultMixin, eventMixin, singlePostMixin ],
	template: `<div style='max-width:1000px;margin-top:30px;'>
			<p><router-link to='/events'>&#8592;List Of Events</router-link></p>
			<div v-if='post.title' class='inpursuit-document' style='margin-bottom:30px;'>
				<div class='inpursuit-event-title'>
					<inpursuit-event-progress :percentage='post.attendants_percentage'></inpursuit-event-progress>
					<div>
						<h1 v-if='post.title'>{{ post.title.rendered }}</h1>
						<div v-if='post.content' class='inpursuit-text-muted' v-html='post.content.rendered'></div>
						<div v-html='listTermsHTML()'></div>
						<router-link :to='editLink()'>Edit</router-link>
						<a href='#' @click='deletePost'>Delete</a>
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
