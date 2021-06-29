var defaultMixin = require( '../mixins/default.js' );

module.exports  = Vue.component( 'inpursuit-select', {
	props: ['field', 'post', 'label'],
	mixins : [defaultMixin],
	template: "<div><label>{{ label }}</label><select @change='updateParent'>" +
    "<option v-for='option in getOptions()' :selected='option.id == getValue()' v-bind:value='option.id' >{{ option.name }}</option>" +
 		"</select></div>",
	methods: {
		getOptions: function(){
			var options = [ { id : '', name : 'Choose ' + this.label } ],
				settings	= this.getSettings();
			if( settings != undefined && settings[ this.field ] != undefined ){
				for( var id in settings[ this.field ] ){
					var option = { id: id, name: settings[ this.field ][ id ] };
					options.push( option );
				}
			}
			return options;
		},
		updateParent: function( event ){
			this.$parent.post[ this.field ] = parseInt( event.target.value );
		},
		getValue: function(){
			var value = 0;
			if( this.post != undefined && this.post[ this.field ] ){
				value = this.post[ this.field ];
			}
			return value;
		},
		/*
		getModel: function(){
			var model = this.post[ this.field ];
			if( Array.isArray( model ) ){ model = model[0]; }
			if( model != undefined ) return model;
			return '';
		}
		*/
	}
} );
