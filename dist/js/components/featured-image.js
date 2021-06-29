module.exports = Vue.component( 'inpursuit-featured-image', {
	props		: ['image_url'],
	template: "<div class='inpursuit-featured-image'><img :src='image_url' /></div>",
} );
