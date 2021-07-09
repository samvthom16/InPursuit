var defaultMixin = require( '../mixins/default.js' );
var memberMixin = require( '../mixins/member.js' );
var singlePostMixin = require( '../mixins/single-post.js' );

module.exports = Vue.component( 'inpursuit-member', {
	mixins	: [ defaultMixin, memberMixin, singlePostMixin ],
	template: `<div style='max-width:960px; margin-top: 30px;'>
			<p><router-link to='/members'>&#8592;List Of Members</router-link></p>
			<div class='inpursuit-grid21' style='margin-bottom:30px;'>
				<div v-if='post.title' class='inpursuit-document'>
					<div class='inpursuit-member-title'>
						<inpursuit-featured-image :image_url='post.featured_image'></inpursuit-featured-image>
						<div>
							<h1 v-if='post.title'>{{ post.title.rendered }}</h1>
							<div v-html='subtitleHTML()'></div>
							<div v-html='listTermsHTML()'></div>
							<router-link :to='editLink()'>Edit</router-link>
							<a href='#' @click='deletePost'>Delete</a>
						</div>
					</div>
				</div>
				<div v-if='metaHTML()' class='inpursuit-document' v-html='metaHTML()'></div>
			</div>
			<timeline :member_id='post_id' per_page='10'></timeline>
		</div>`,
} );
