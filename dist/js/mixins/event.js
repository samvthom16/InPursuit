var endpoints = require( '../lib/endpoints.js' );

module.exports = {
	data(){
		return {
			url					: endpoints.events,
			filterTerms	: {
				event_type : {
					slug	: 'event_type',
					label	: 'All Event Types',
				},
				location : {
					slug	: 'location',
					label	: 'All Locations',
				}
			},
		}
	},
	methods: {
		genderAgeText: function( post ){
			var gender 	= post['gender'] != null ? post['gender'] : "",
				age 			= post['age'] != null ? post['age'] : "",
				meta 			= [],
				subtitle 	= '';

			if( gender.length ) meta.push( gender );
			if( age.length ) meta.push( age + ' Years' );

			if( meta.length ) subtitle = meta.join( ', ' );
			return subtitle;
		},
		listTermsHTML: function(){

			var html = "<ul class='post-terms'>";

			var singleFields = ['location', 'event_type'];

			for( var index in singleFields ){
				var field = singleFields[ index ];
				if( this.post[field] ){
					html += "<li class='badge " + field + "'>" + this.getTermName( field, this.post[field] ) + "</li>";
				}
			}

			html += "</ul>";
			return html;
		}
	}
};
