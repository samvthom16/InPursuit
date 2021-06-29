module.exports = Vue.component( 'inpursuit-event-progress', {
	props		: ['percentage'],
	template: '<div class="participation-wrapper"><div class="single-chart">' +
			'<svg viewBox="0 0 36 36" class="circular-chart blue">' +
			'<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />' +
			'<path class="circle" :stroke-dasharray="stroke()" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />' +
			'<text x="18" y="20.35" class="attending-percentage">{{ html() }}</text>' +
			'</svg></div></div>',
	methods: {
		html: function(){
			return this.percentage + "%";
		},
		stroke: function(){
			return this.percentage + ", 100";
		}
	}
} );
