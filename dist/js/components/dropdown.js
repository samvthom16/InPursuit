var debounceMixin = require( '../mixins/debounce.js' );
var defaultMixin = require( '../mixins/default.js' );

var vuejsDropdown = require( '../vuejs-dropdown.js' );
Vue.use( Dropdown );

module.exports = Vue.component( 'inpursuit-dropdown', {
	props		: ['placeholder', 'slug', 'selectCallback'],
	mixins	: [ defaultMixin, debounceMixin ],
	template: '<Dropdown :options="getOptions()" :disabled="false" v-on:selected="debounceEvent" :maxItem="10" :placeholder="placeholder"></Dropdown>',
	methods	: {
		debounceCallback: function( option ){
			if( option.id != undefined ){
				// CALLBACK FROM THE PROPS
				this.selectCallback( this.slug, option.id );
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
