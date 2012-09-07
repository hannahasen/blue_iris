<?php
//The loop generation function
function video_looper($category_name = '', $num_posts = 9 ){

	$vid_paged = (get_query_var('page')) ? get_query_var('page') : 1;
	
	
	$get_q_args =	( 
	 		array(
	 		'post_type' => 'video',
			'post__not_in'  => get_option( 'sticky_posts' ),
			'category_name' => $category_name,
		 	'paged' => $vid_paged,
			'orderby' => 'date',
			'order' => 'DESC',			
			'posts_per_page' => $num_posts,

		));
		
		
			$vid_query = new WP_Query( $get_q_args );	
	?>		
		<ul>
			<?php 	if ($vid_query->have_posts()): while ($vid_query->have_posts()): $vid_query->the_post();  ?>
	<?php 
	global $post;
	$embed_link = get_post_meta($post->ID, 'embed_link', true);
	$thumb = get_post_meta( $post->ID, 'video_thumb', true);
	// $host = get_post_meta( $post->ID, 'host_name', true);

	
	 ?>
	 <!-- This would allow for video to open in a colorbox -->	
		<li>
			<a href='<?php echo $embed_link ?>' class="colorbox-link"> 
					<!-- <a href="<?php the_permalink();  ?>">	 -->
					
					<div class="post">
						<div class="thumb_container <?php echo $host ?>"><?php if ( function_exists( 'get_the_image' ) ) get_the_image( array( 'meta_key' => array( 'video_thumb', 'thumbnail' ), 'size' => 'thumbnail', 'width' => '230', 'image_class' => 'vid_thumb', ) ); ?>
						</div> <!-- thumb container -->

						<div class="text">
							<a href="<?php the_permalink(); ?>"><h2><?php the_title();?></h2></a>
							<?php the_excerpt(); ?>
						</div> <!--text end-->
					
					</div> 	<!-- post--> 
			</a>
		</li>

				<?php endwhile; ?>
				<?php endif; ?>
		</ul>
	
				
<?php posts_nav_link(',','Newer Posts','Older Posts'); ?>

<?php } //end video looper
	
	?>