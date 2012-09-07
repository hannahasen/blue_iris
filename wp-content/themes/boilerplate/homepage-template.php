<?php
/*
Template Name: Homepage Template */
?>

<?php get_header(); ?>
	
<article>
<div class="page_text">
<h2>blue iris films</h2>	
<p>
Blue Iris Films is a production company based in Scotland.  We location scout, manage, crew, find funding, acquire permissions and licenses, organise food and accommodation, and assist with and develop different kinds of film project. We have worked with many different companies and individuals both in Scotland and worldwide.
</p>
<p>
Run by producers Katie Crook and Olivia Gifford, Blue Iris Films is also looking to create close and rewarding relationships
with writers, directors and various crew talents.
</p>
<p>
What do you need?
</p>
</div><!-- end of page_text-->



<ul class="videos">
<li>
	<a href="#">
		<img src="http://placehold.it/225x135">
		<h3>Short Films</h3>
	</a>
</li>

<li><a href="#"><img src="http://placehold.it/225x135"><h3>Corporates and Promotional</h3></a></li>

<li><a href="#"><img src="http://placehold.it/225x135"><h3>Music Videos</h3></a></li>

<li><a href="#"><img src="http://placehold.it/225x135"><h3>Event Coverage</h3></a></li>

<li><a href="#"><img src="http://placehold.it/225x135"><h3>Feature Films</h3></a></li>

<li><a href="#"><img src="http://placehold.it/225x135"><h3>Documentary</h3></a></li>

</ul>

</article>

<footer>

	<div class="logo_footer">		
		<img src="http://placehold.it/25x30" alt="Blue Iris Logo" class="photo" />
	</div>	
		
		<span class="bio">COPYRIGHT BLUE FILMS 2012 . Background image courtesy of <a href="...">Anthony Devine</a> - 'Boat' matte painting, 2012 - <a href="http://www.boatthefilm.com">www.boatthefilm.com</a></span>

</footer>
		
</body>
</html>





		
			<div class="frontpage">
				<?php 	if ($wp_query->have_posts()): while ($wp_query->have_posts()): $wp_query->the_post();  ?>
			
				<a href="<?php the_permalink(); ?>">	
					<div class="post">
						<h2><?php the_title();?></h2>
						<div class=""><?php the_post_thumbnail('medium'); ?></div>
					</div> <!-- post--></a>
		

			<?php endwhile; else: wp_reset_query(); ?>
			<?php endif; ?>


						<div class="navigation"><p><?php// posts_nav_link(); ?></p></div>

						<?php posts_nav_link(',','Newer','Older'); ?>

						</div> <!--- end of front page-->
				
<?php // get_sidebar(); ?>
<?php get_footer(); ?>