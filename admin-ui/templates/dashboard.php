<?php
	function getTotalPosts( $post_type ){
		$count_posts = wp_count_posts( $post_type );
		$total_posts = $count_posts->publish;
		return $total_posts;
	}

	$post_types = array(
		'inpursuit-members' => 'Members',
		'inpursuit-events'	=> 'Events'
	);
?>

	<ul id='statistics'>
		<?php foreach( $post_types as $post_type => $title ):?>
		<li>
			<span><?php echo $title;?></span>
			<span><?php echo getTotalPosts( $post_type );?></span>
		</li>
		<?php endforeach;?>
	</ul>

	

	<style>
		#statistics li{
			display: grid;
			grid-template-columns: 150px 1fr;
		}
	</style>
