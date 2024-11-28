var endpoints = require( '../lib/endpoints.js' );
var defaultMixin = require( '../mixins/default.js' );
var managerMixin = require( '../mixins/manager.js' );

var API = require( '../lib/api.js' );

require( '../form-fields/checkbox.js' );

module.exports = {
	mixins	: [ defaultMixin, managerMixin ],
	template: `<div class='inpursuit-form' style='margin-top:30px;'>
				<p v-if="errors.length" style="color: red;font-weight: 500;margin-top: 5px;">
					<b>Please correct the following error(s):</b>
					<ul style="list-style: disc;margin-left: 16px;">
						<li v-for="error in errors">{{ error }}</li>
					</ul>
			</p>
			<div class='inpursuit-grid2'><div class='inpursuit-form-field' v-for='metafield in metafields'>
				<label>{{ metafield.label }}</label><input v-model='post[metafield.name]' type='text' />
			</div></div>

			<div class='inpursuit-form-field inpursuit-grid2'>
				<div>
					<label>Manager Role</label>
					<select v-model='post.roles[0]'>
						<option v-for="role in getRoles()" v-bind:value="role.slug">{{role.name}}</option>
					</select>
				</div>
			</div>

			<div class='inpursuit-form-field inpursuit-grid2' v-if='show_element.multiselect'>
				<inpursuit-checkbox v-for='multiselect in multiselects' :field='multiselect.field' :label='multiselect.label' :post='post'></inpursuit-checkbox>
			</div>

			<div class='inpursuit-form-field' style='margin-top: 40px;'>
				<p>
					<button class='button' type='button' @click='savePost()' v-if='show_element.save'>Save Changes</button>
					<button class='button' type='button' @click='updatePost()' v-if='show_element.update'>Update Changes</button>
		 			or <router-link :to='getPostLink( post )'>Cancel</router-link>
					<span class='spinner' :class='{active: loading}'></span>
				</p>
			</div>
		</div>`,
	data(){
		return {
			show_element	: {},
			post					: {
				email: '',
				roles: ['subscriber']
			},
			errors				: [],
			post_id				: 0,
			loading				: true,
			multiselects : [],
		}
	},
	methods: {
		getURL: function(){
			if( this.post_id ){
				return endpoints['users'] + "/" + this.post_id;
			}
			return endpoints.users;
		},
		getPost	: function(){
			var component = this;
			API.request( {
				url					: this.getURL(),
				params			: { context: 'edit' },
				callbackFn	: function( response ){

					// STOP THE LOADER
					component.loading = false;

					if( response.status == 200 || response.status == 201 ){
						component.post = response.data;
					} else {
						component.errors.push(response.data?.message ?? "Something went wrong.")
					}

				}
			} );
		},
		getRoles:function(){
			return [
				{ slug: 'subscriber', name: 'Subscriber' },
				{ slug: 'contributor', name: 'Contributor' },
				{ slug: 'author', name: 'Author' },
				{ slug: 'editor', name: 'Editor' },
				{ slug: 'administrator', name: 'Administrator' }
			];
		},
		getMangerData :function(){
			var component	= this;
			return Object.assign( {}, component.post );
		},
		isEmptyField: function( field ){
			if( field && field.replace(/^\s+|\s+$/gm,'') ){
				return false;
			}
			return true;
		},
		isvalidEmail: function( email ){

			var email_format = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

			if(email.match(email_format)){
				return true;
			}
			else{
				// THROW ERROR IF EMAIL IS EMPTY
				this.errors.push("Invalid Email");

				return false;
			}

		},
		savePost: function(){

			this.errors = [];

			var newManager = this.getMangerData();

			// console.log(newManager);

			// SET USERNAME = EMAIL_ID
			if( this.isvalidEmail( newManager.email ) ){
				newManager['username'] = newManager.email;
			}

			// THROW ERROR IF PASSWORD IS EMPTY
			if( this.isEmptyField( newManager.password ) ){
				this.errors.push("Invalid Password");
			}

			this.sendRequest( 'post', newManager );

		},
		updatePost : function(){

			this.errors = [];

			var manager = this.getMangerData();

			if( manager.first_name || manager.last_name ){

				var name = manager.first_name ? ( manager.last_name ? `${manager.first_name} ${manager.last_name}` : manager.first_name ) : ( manager.last_name ? manager.last_name : ""  );

				// UPDATE MANAGER NAME IF FIRST OR LAST NAME EXISTS
				if( ! this.isEmptyField( name ) ){
					manager['name'] = name;
				}

			}

			this.sendRequest( 'post', manager )

		},

		sendRequest: function( type, managerData ){

			// SEND REQUEST IF THERE ARE NO ERRORS
			if (!this.errors.length) {

				var component	= this;

				component.loading = true;

				API.request( {
					method	: type,
					data 		: managerData,
					url			: this.getURL(),
					callbackFn: function( response ){

						component.loading = false;

						if( response.status == 200 || response.status == 201 ){

							component.post_id = response.data.id;

							component.post = response.data;

							// REDIRECT TO THE SINGLE PAGE
							component.$router.push( component.getPostLink( component.post ) );
						} else if( response.data.code == "rest_user_invalid_email" || response.data.code == "existing_user_login" || response.data.code == "existing_user_email" ){
							component.errors.push("Email already exists!");
						} else {
							component.errors.push(response.data?.message ?? "Something went wrong.")
						}

					}
				} );

			}

		},
		init: function(){}
	},
	created: function(){
		this.init();
		var post_id = this.$route.params.id;

		if( post_id ){
			this.post_id = post_id;
			this.getPost();
		} else{
			this.loading = false;
		}

		// CHECK IF POST INFORMATION HAS BEEN PASSED IN THE ROUTE
		// THIS CAN BE USED AS CACHE
		if( this.$route.params.post != undefined ){
			this.post = this.$route.params.post;
		}

	}
};
