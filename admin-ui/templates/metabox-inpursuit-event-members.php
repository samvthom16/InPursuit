<p>
	<input type="text" name="search" @input="debounceSearch" placeholder="Search" /><span class="spinner" :class="{active: loading}"></span>
	<label><input type="checkbox" v-model='show_event_attendants' @click="refreshPosts" />Attendants Only</label>
</p>
<ul class='posts-list'>
	<li class='post-item' :class="{selected: post.attended}" v-for="post in posts">
		<div class="post-row">
			<div class="post-item-toggle" @click="toggleSelect(post)">
				<span class="slider round"></span>
			</div>
			<div class="post-content">
				<h3>{{ post.title.rendered }}</h3>
				<p v-if="post.age" class='meta'>{{ post.age }} Years Old</p>
			</div>
		</div>
		<div class="post-terms">
			<span class="badge" :class="term.taxonomy" v-for="term in post.terms">{{ term.name }}</span>
		</div>
  </li>
</ul>
<nav aria-label="Page navigation example" v-if="pages.length > 1">
	<ul class="pagination">
		<li class="page-item">
			<button type="button" class="page-link" v-if="page != 1" @click="page--"> Previous </button>
		</li>
		<li class="page-item">
			<button type="button" class="page-link" :class="{active: page === pageNumber}" v-for="pageNumber in pages" @click="page = pageNumber"> {{pageNumber}} </button>
		</li>
		<li class="page-item">
			<button type="button" @click="page++" v-if="page < pages.length" class="page-link"> Next </button>
		</li>
	</ul>
</nav>
