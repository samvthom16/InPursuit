var defaultMixin = require( '../mixins/default.js' );
var optionsMixin = require( '../mixins/options-form-field.js' );

module.exports  = Vue.component( 'inpursuit-checkbox', {
	props: ['field', 'post', 'label'],
	mixins : [defaultMixin, optionsMixin ],
	template: "<div><label>{{ label }}</label><ul>" +
    "<li v-for='option in getOptions()'><label><input :value='option.id' v-model='post[field]' type='checkbox' />{{ option.name }}</label></li>" +
 		"</ul></div>",
} );
