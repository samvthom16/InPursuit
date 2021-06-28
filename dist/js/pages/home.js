module.exports = Vue.component( 'home', {
	template: "<div>Hello World</div>",
	created: function(){
		this.$router.push( '/dashboard' );
	}
} );
