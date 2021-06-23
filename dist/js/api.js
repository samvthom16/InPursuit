var API = function(){

	var self = {
		base_url 	: inpursuitSettings.root,
	};

	function getOptions( options ){
		return Vue.util.extend( {
			method			: 'get',
			post_type		: 'posts',
			id					: '',
			slug 				: '',
			url  				: '',
			callbackFn	: function(){}
		}, options );
	}

	function updateURL( url, params ){
		var i = 0;
		for ( var key in params ) {
			if( i == 0 ) url += "?";
			else url += "&";
			url += key + "=" + params[key];
			i++;
		}
		return url;
	}

	self.request = function( options ){
		var url = self.base_url + options.url;
		if( options.params != undefined ){
			url = updateURL( url, options.params );
		}

		var headers = {
			'X-WP-Nonce': inpursuitSettings.nonce
		};

		var api_obj;
		if( options.method == 'post' ){
			//console.log( url );
			api_obj = axios.post( url, options.data, { headers: headers } );
		}
		else if( options.method == 'delete' ){
			//console.log( headers );
			api_obj = axios.delete( url, { data: options.data, headers: headers } );
		}
		else{
			api_obj = axios.get( url, { headers: headers } );
		}

		api_obj.then( function( response ){
			//console.log( response );
			if ( typeof options.callbackFn === 'function' ) {
				options.callbackFn( response );
			}
		} );
	};

	return self;
};
