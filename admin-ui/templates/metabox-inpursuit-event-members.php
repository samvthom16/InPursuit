All ({{total}}) | Selected ({{total_selected}})
<ul>
	<li class='post-item' :class="{selected: post.selected}" v-for="post in posts">
		<div class="post-item-toggle" @click="toggleSelect(post)">
			<span class="slider round"></span>
		</div>
		<div class="post-content"><h3>{{ post.title.rendered }}</h3></div>
  </li>
</ul>
