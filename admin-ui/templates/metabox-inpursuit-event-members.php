<!--
<p class="inpursuit-search-filters">
	<inpursuit-search-text :searchQuery="searchQuery"></inpursuit-search-text>
	<inpursuit-dropdown v-for="term in filterTerms" :settings="settings" :slug="term.slug" :placeholder="term.label"></inpursuit-dropdown>
	<span class="spinner" :class="{active: loading}"></span>
	<label><input type="checkbox" v-model='show_event_attendants' @click="refreshPosts" />Attendants Only</label>
</p>
<ul class='posts-list'>
	<li class='post-item' :class="{selected: post.attended}" v-for="post in posts">
		<div class="post-row">
			<div class="post-item-toggle" @click="toggleSelect(post)">
				<span class="slider round"></span>
			</div>
			<div class="post-content">
				<h3><a :href="post.edit_url" target="_blank">{{ post.title.rendered }}</a></h3>
				<p class='inpursuit-text-muted'>{{ genderAgeText(post) }}</p>
			</div>
		</div>
		<div class="post-terms">
			<span class='badge inpursuit-location' v-if='post.location.length > 0'><span class='dashicons dashicons-location'></span>{{ locationText(post) }}</span>
		</div>
  </li>
</ul>
<inpursuit-page-pagination :total_pages="total_pages"></inpursuit-page-pagination>

-->
