var defaultMixin = require( '../mixins/default.js' );
var optionsMixin = require( '../mixins/options-form-field.js' );

module.exports  = Vue.component( 'inpursuit-select', {
	props: ['field', 'post', 'label'],
	mixins : [defaultMixin, optionsMixin],
	template: "<div><label>{{ label }}</label><select v-model='post[field]'>" +
    "<option v-for='option in getOptions()' v-bind:value='option.id' >{{ option.name }}</option>" +
 		"</select></div>",
	methods: {
		getDefaultOption: function(){
			return { id : '', name: 'Choose' };
		}
	}
} );
