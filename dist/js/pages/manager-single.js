var managerEditMixin = require( '../mixins/manager-edit.js' );
var singleUserMixin = require( '../mixins/single-user.js' );

module.exports = Vue.component( 'inpursuit-managers', {
	mixins	: [ singleUserMixin, managerEditMixin ],
	template: `<div style='max-width:960px; margin-top: 30px;'>
			<p><router-link to='/managers'>&#8592;List Of Managers</router-link></p>
			<div class='inpursuit-grid21' style='margin-bottom:30px;'>
				<div v-if='post.name' class='inpursuit-document'>
					<div class='inpursuit-member-title' style='position:relative;'>
						<inpursuit-featured-image :image_url='post.avatar_urls[48]'></inpursuit-featured-image>
						<div>
							<h1 v-if='post.name'>{{ post.name }}</h1>
							<inpursuit-user-actions :post='post' :actionCallback='actionCallback'></inpursuit-manager-actions>
						</div>
					</div>
				</div>
				<div v-if='metaHTML()' class='inpursuit-document' v-html='metaHTML()'></div>
			</div>
		</div>`,
} );
