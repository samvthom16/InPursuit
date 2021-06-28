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

			if( this.post.location != undefined && this.post.location.length > 0 ){
				html += "<li class='badge inpursuit-location'>" + this.post.location.join( ', ' ) + "</li>";
			}

			if( this.post.event_type != undefined && this.post.event_type.length > 0 ){
				html += "<li class='badge inpursuit-event-type'>" + this.post.event_type.join( ', ' ) + "</li>";
			}

			html += "</ul>";
			return html;
		}
	}
};
