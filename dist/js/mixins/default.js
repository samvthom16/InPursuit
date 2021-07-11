var endpoints = require( '../lib/endpoints.js' );

var API = require( '../lib/api.js' );

module.exports = {
	data	: function(){
		return {
			settings		: {}
		}
	},
	filters: {
	  moment: function (date) {
			return moment(date).fromNow();
	  }
	},
	methods: {
		getSettings: function(){
			return window['inpursuit_settings'];
		},
		getTermName: function( field, term_id ){
			var settings = this.getSettings();
			if( settings != undefined && settings[ field ] && settings[ field ][ term_id ] ){
				return settings[ field ][ term_id ];
			}
			return '';
		},
		listTermNames: function( field, term_id_arr ){
			var names = [];
			for( var index in term_id_arr ){
				var term_name = this.getTermName( field, term_id_arr[index] );
				names.push( term_name );
			}
			return names;
		},
		getPostLink: function( post ){
			var route = {
				name		: "",
				params 	: { id : post.id, post: post }
			};
			if( post.type != undefined && post.type == 'inpursuit-members' ){
				route.name = "SingleMember";
			}
			else if( post.type != undefined && post.type == 'inpursuit-events' ){
				route.name = "SingleEvent";
			}
			return route;
		}
	}
};
