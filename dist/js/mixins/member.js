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

			if( this.post.location ){
				html += "<li class='badge inpursuit-location'>" + this.getTermName( 'location', this.post.location ) + "</li>";
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
		},
		metaHTML: function(){
			var html = '';
			var fields = [
				{ field : 'email', text : '', type: 'meta' },
				{ field : 'phone', text : '', type: 'meta' },
				{ field : 'birthday', text : 'Born on ', type: 'special-events' },
				{ field : 'wedding', text : 'Got married on ', type: 'special-events' }
			];

			for( var i=0; i<fields.length; i++ ){
				var field = fields[i]['field'];
				var type = fields[i]['type'];

				var value;
				if( type == 'special-events' && this.post.special_events && this.post.special_events[field] ){
					var value = moment( this.post.special_events[field] ).format('LL');
				}
				else{
					value = this.post[field];
				}

				if( value ){
					var text = fields[i]['text'] + "<span>" + value + "</span>";
					var classes = 'inpursuit-meta ' + field;
					html += "<p class='" + classes + "'>" + text + "</p>";
				}
			}

			if( html ){
				html = "<h4 class='inpursuit-meta-headline'>Additional Information</h4>" + html;
			}

			return html;
		},
	},

};
