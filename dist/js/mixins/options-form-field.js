module.exports = {
	methods: {
		getOptions: function(){
			var options 		= [],
				defaultOption = this.getDefaultOption(),
				settings			= this.getSettings();

			if( defaultOption ){
				options.push( defaultOption );
			}

			if( settings != undefined && settings[ this.field ] != undefined ){
				for( var id in settings[ this.field ] ){
					var option = { id: parseInt( id ), name: settings[ this.field ][ id ] };
					options.push( option );
				}
			}
			return options;
		},
		getValue: function(){
			var value = 0;
			if( this.post != undefined && this.post[ this.field ] ){
				value = this.post[ this.field ];
			}
			return value;
		},
		getDefaultOption: function(){
			return false;
		}
	}
};
