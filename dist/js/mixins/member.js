var endpoints = require( '../lib/endpoints.js' );

module.exports = {
	data(){
		return {
			url					: endpoints.members,
			filterTerms	: {
				gender : {
					slug	: 'gender',
					label	: 'All Gender',
				},
				member_status : {
					slug	: 'member_status',
					label	: 'All Status',
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
		locationText: function( post ){
			return post.location.join( ', ' );
		},
	}
};
