module.exports = Vue.component( 'template-dashboard', {
	template: "<div class='inpursuit-grid3' style='margin-top: 30px;'>" +
	"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Recent Members</h4><latest-updates per_page='7' post_type='members'></latest-updates></div>" +
	"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Recent Events</h4><latest-updates per_page='7' post_type='history'></latest-updates></div>" +
	"<div class='inpursuit-dashboard'><h4 class='inpursuit-dashboard-title'>Demographic</h4><inpursuit-choropleth-map></inpursuit-choropleth-map></div>"	+
	"</div>"
} );
