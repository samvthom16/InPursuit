var debounceMixin = require( '../mixins/debounce.js' );
var defaultMixin = require( '../mixins/default.js' );

var vuejsDropdown = require( '../vuejs-dropdown.js' );
Vue.use( Dropdown );

module.exports = Vue.component( 'inpursuit-dropdown', {
	props		: ['settings', 'placeholder', 'slug'],
	mixins	: [ defaultMixin, debounceMixin ],
	template: '<Dropdown :options="getOptions()" :disabled="false" v-on:selected="debounceEvent" :maxItem="10" :placeholder="placeholder"></Dropdown>',
	methods	: {
		debounceCallback: function( option ){
			if( option.id != undefined ){
				this.$parent.filterTerms[ this.slug ]['value'] = option.id;
				if( this.$parent.page != undefined ){ this.$parent.page = 1;}
				this.$parent.getPosts();
			}
		},
		getOptions: function(){
			var options = [{
				id 	: '0',
				name: this.placeholder
			}];
			var slug 		= this.slug,
				settings 	= this.getSettings();

			if( settings[slug] != undefined ){
				for( var key in settings[slug] ){
					options.push( {
						'id'		: key,
						'name'	: settings[slug][key]
					} );
				}
			}
			return options;
		}
	}
} );
