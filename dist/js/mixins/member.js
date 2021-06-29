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
			var age 		= post['age'] != null ? post['age'] : "",
				meta 			= [],
				subtitle 	= '';

			var gender = this.getTermName( 'gender', post['gender'] );

			if( gender.length ) meta.push( gender );
			if( age.length ) meta.push( age + ' Years' );

			if( meta.length ) subtitle = meta.join( ', ' );
			return subtitle;
		},
		listTermsHTML: function(){

			var html = "<ul class='post-terms'>";

			if( this.post.location != undefined && this.post.location.length > 0 ){
				html += "<li class='badge inpursuit-location'>" + this.listTermNames( 'location', this.post.location ).join( ', ' ) + "</li>";
			}

			if( this.post.group != undefined && this.post.group.length > 0 ){
				html += "<li class='badge inpursuit-group'>" + this.listTermNames( 'group', this.post.group ).join( ', ' ) + "</li>";
			}

			if( this.post.profession != undefined && this.post.profession.length > 0 ){
				html += "<li class='badge inpursuit-profession'>" + this.listTermNames( 'profession', this.post.profession ).join( ', ' ) + "</li>";
			}

			var genderAge = this.genderAgeText( this.post );
			if( genderAge.length > 0 ){
				html += "<li class='badge inpursuit-gender'>" + genderAge + "</li>";
			}

			html += "</ul>";
			return html;
		},
		subtitleHTML: function(){
			return "<p class='inpursuit-text-muted'>" + this.getTermName( 'member_status', this.post.member_status ) + "</p>";
		}
	},

};
