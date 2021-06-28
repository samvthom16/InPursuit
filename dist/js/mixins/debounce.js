module.exports = {
	data(){
		return {
			debounce: null,
		}
	},
	methods: {
		debounceCallback: function( event ){},
		debounceEvent		: function( event ) {
			clearTimeout( this.debounce );
      this.debounce = setTimeout(() => {
				this.debounceCallback( event );
			}, 600);
    }
	}
};
