var endpoints = require( '../lib/endpoints.js' );
var API = require( '../lib/api.js' );

module.exports = Vue.component( 'inpursuit-select-image', {
	props: ['post'],
	data(){
		return {
			loading: false
		}
	},
	template: `<div style='position:relative; width:200px;'>
			<img :src='post.featured_image' style='width:100%;height:auto;' />
			<div style='position:absolute; background-color: #0005; top: 0; left: 0; width: 100%; height: 100%;'></div>
			<button type='button' class='button' style='position:absolute; left:50%; top: 50%; transform:translate(-50%, -50% )' v-html='loading ? "Uploading" : "Change" '></button>
			<input style='position: absolute; width: 100%; height:100%; opacity:0; top: 0; left:0;' type='file' @change='change' accept='image/*' />
		</div>`,
	methods: {
		change: function( ev ){
			var component = this,
				formData 		= new FormData(),
				file 				= ev.target.files[0];

			component.$parent.post.featured_image = URL.createObjectURL( file );
			component.loading = true;

			formData.append( 'file', file );

			API.request( {
				method	: 'post',
				data 		: formData,
				url			: endpoints.media,
				callbackFn: function( response ){
					console.log( response.data );
					component.$parent.post.featured_media = response.data.id;
					component.loading = false;
				}
			} );

		},
	},
} );
