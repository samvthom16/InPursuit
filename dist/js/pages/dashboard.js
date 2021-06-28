module.exports = Vue.component( 'template-dashboard', {
	template: "<div class='inpursuit-grid3' style='margin-top: 30px;'>" +
	"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Recent Members</h4><latest-updates per_page='5' post_type='inpursuit-members'></latest-updates></div>" +
	"<div class='inpursuit-dashboard'><h4 class='inpurs
	uit-dashboard-title'>Recent Events</h4><latest-updates per_page='5' post_type='inpursuit-events'></latest-updates></div>" +
	"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Demographic</h4><inpursuit-choropleth-map></inpursuit-choropleth-map></div>"	+
	"</div>"
} );
