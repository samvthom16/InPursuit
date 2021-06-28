Vue.component( 'special-event', {
	props	: ['title', 'value', 'slug'],
  data	: function () {
    return {
      showFlag: false
    }
  },
  template: '<div><label><input type="checkbox" name="flag" v-model="showFlag" />Add {{ title }}</label><p v-if="showFlag"><input :name="slug" :value="value" type="date" /></p></div>',
	created	: function(){
		if( this.value != 0 ) this.showFlag = true;
	}
} );
