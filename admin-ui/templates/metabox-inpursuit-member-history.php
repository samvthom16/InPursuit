<div class="timeline">
  <div class="container-right" v-for="post in posts">
    <div class="content">
      <h4>{{post.date | moment }}</h4>
      <p>{{ post.title.rendered }}</p>
    </div>
  </div>
</div>
