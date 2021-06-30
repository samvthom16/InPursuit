var defaultMixin = require( '../mixins/default.js' );
var optionsMixin = require( '../mixins/options-form-field.js' );

//var checkbox = require('vue-material-checkbox');
//Vue.use(checkbox);

module.exports  = Vue.component( 'inpursuit-checkbox', {
	props: ['field', 'post', 'label'],
	mixins : [defaultMixin, optionsMixin ],
	template: "<div><label>{{ label }}</label><ul>" +
    //"<checkbox v-for='option in getOptions()' :value='option.id' v-model='post[field]'>{{ option.name }}</checkbox>" +
		"<li v-for='option in getOptions()'><label><input :value='option.id' v-model='post[field]' type='checkbox' />{{ option.name }}</label></li>" +
 		"</ul></div>",
} );
