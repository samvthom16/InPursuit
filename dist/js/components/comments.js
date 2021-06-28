var API = require( '../api.js' );

module.exports = Vue.component( 'add-comment', {
	template	: "<div><button type='button' class='button' @click='openForm()'>Add Comment</button><div class='thickbox-modal' :class='status'><div class='thickbox-modal-content'><header>Add Comment<button type='button' class='close-btn' @click='closeForm()'>&times;</button></header><p><textarea v-model='comment.comment'></textarea></p><p><button type='button' class='button' @click='saveForm()'>Submit</button><span class='spinner' :class='{active: loading}'></span></p></div></div></div>",
	props			: [ 'comment_id', 'post_id' ],
	data			: function(){
		return {
			loading	: false,
			status	: 'closed',
			comment	: {}
		}
	},
	methods	: {
		getUrl: function(){
			var url = endpoints.comments;
			if( this.comment_id ){
				url += this.comment_id;
			}
			return url;
		},
		openForm	: function(){ this.status = 'open'; },
		closeForm	: function(){ this.status = 'closed'; },
		saveForm	: function(){
			var comment = this.comment;
			comment.post = this.post_id;

			if( !comment.comment ){
				alert( 'Comment cannot be empty!' );
			}
			else{
				this.loading = true;
				var component = this;

				API.request( {
					method	: 'post',
					url			: this.getUrl(),
					data		: comment,
					callbackFn	: function( response ){
						component.loading = false;
						component.status = 'closed';
						component.$parent.refreshPosts();
						component.comment.comment = '';
					}
				} );
			}
		}
	}
} );
