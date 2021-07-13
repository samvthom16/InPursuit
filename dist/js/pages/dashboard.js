Vue.component( 'inpursuit-calendar', {
	template: `<div>
			<ul class='list-inline'>
				<li><button type='button' @click='setCalendar'>Previous</button></li>
				<li><button type='button' @click='setCalendar'>Next</button></li>
				<li v-html='getRenderRangeText()'></li>
				<li><button type='button' @click='setCalendar'>Today</button></li>
			</ul>
			<div id='calendar'></div>
		</div>`,
	data(){
		return {
			calendar: ''
		}
	},
	methods: {

		getRenderRangeText: function() {

			var cal = this.calendar;
			var html = [];

			if( cal ){
				var options = cal.getOptions();
	  		var viewName = cal.getViewName();

	  		if ( viewName === 'day' ) {
	    		html.push(moment(cal.getDate().getTime()).format('YYYY.MM.DD'));
	  		}
				else if ( viewName === 'month' &&
	    		(!options.month.visibleWeeksCount || options.month.visibleWeeksCount > 4)) {
	    		html.push( moment( cal.getDate().getTime() ).format( 'MMMM YYYY' ) );
	  		}
				else {
	    		html.push(moment(cal.getDateRangeStart().getTime()).format('YYYY.MM.DD'));
	    		html.push(' ~ ');
	    		html.push(moment(cal.getDateRangeEnd().getTime()).format(' MM.DD'));
	  		}
			}

  		return html.join('');
		},

		setCalendar: function( ev ){

			var action = ev.target.innerHTML;

			if( action == 'Previous' ){
				this.calendar.prev();
			}
			else if( action == 'Next' ){
				this.calendar.next();
			}
			else if ( action == 'Today' ){
				this.calendar.today();
			}


		}
	},
	created: function(){

		var component = this;

		require.ensure(['tui-calendar'], function( ){

			// LOAD THE REQUIRED CODE
			var tui = require( 'tui-calendar' );

			component.calendar = new tui('#calendar', {
				defaultView: 'month',
				taskView: true,
				isReadOnly: true
			} );

		} );
	}
} );

module.exports = Vue.component( 'template-dashboard', {
	template: `<div>
			<div class='inpursuit-grid3' style='margin-top: 30px;'>
				<div class='inpursuit-dashboard'>
					<h4 class='inpursuit-dashboard-title'>Recent Members</h4>
					<latest-updates per_page='7' post_type='members'></latest-updates>
				</div>
				<div class='inpursuit-dashboard'>
					<h4 class='inpursuit-dashboard-title'>Recent Events</h4>
					<latest-updates per_page='7' post_type='history'></latest-updates>
				</div>
				<div class='inpursuit-dashboard'>
					<h4 class='inpursuit-dashboard-title'>Demographic</h4>
					<inpursuit-choropleth-map></inpursuit-choropleth-map>
				</div>
			</div>
			<div class='inpursuit-dashboard' style='margin-top: 30px;'>
				<h4 class='inpursuit-dashboard-title'>Calendar</h4>
				<inpursuit-calendar></inpursuit-calendar>
			</div>
		</div>`
} );
