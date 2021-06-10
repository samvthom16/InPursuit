<?php /*
<div id="my-content-id" style="display:none;">
 <p>
	 <textarea></textarea>
 </p>
</div>

<label>Additional Notes</label>
<p><textarea rows="5" style="max-width: 500px;width: 100%;padding: 10px;"></textarea></p>
<a href="#TB_inline?width=400&height=350&inlineId=my-content-id" class="thickbox button">Submit</a>
*/ ?>

<div class="timeline" style="margin-top:20px;margin-left: 20px;">
  <div class="container-right" v-for="post in posts">
    <div class="content">
      <h4>{{post.date | moment }}</h4>
      <p>{{ post.title.rendered }}</p>
			<div class="post-terms">
				<span class="badge" :class="term.taxonomy" v-for="term in post.terms">{{ term.name }}</span>
			</div>
    </div>
  </div>
</div>
<p><span class="spinner" :class="{active: loading}"></span></p>
<p v-if="page < total_pages"><button type="button" class="button" @click="page++">Load More</button></p>
