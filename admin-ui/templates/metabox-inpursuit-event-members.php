All ({{total}}) | Selected ({{total_selected}})
<p><input type="text" name="search" @input="debounceSearch" placeholder="Search" /></p>
<ul>
	<li class='post-item' :class="{selected: post.attended}" v-for="post in posts">
		<div class="post-item-toggle" @click="toggleSelect(post)">
			<span class="slider round"></span>
		</div>
		<div class="post-content"><h3>{{ post.title.rendered }}</h3></div>
  </li>
</ul>
