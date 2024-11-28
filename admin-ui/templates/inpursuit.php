<div class="wrap">
	<div id="inpursuit-app" style="margin-top:50px;">
		<h2 class="nav-tab-wrapper">
			<router-link class="nav-tab" to="/dashboard">Dashboard</router-link>
			<router-link class="nav-tab" to="/members">Members</router-link>
			<router-link class="nav-tab" to="/events">Events</router-link>
			<router-link class="nav-tab" to="/managers">Managers</router-link>
		</h2>
		<router-view v-if='!loading'></router-view>
		<p><span class='inpursuit-spinner spinner' :class='{active: loading}'></span></p>
	</div>
</div>
