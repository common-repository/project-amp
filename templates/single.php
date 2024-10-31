<?php
amp_get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();

				// Include the page content template.
				amp_get_template_part( 'content' , 'single' );

				// If comments are open or we have at least one comment, load up the comment template.

				// End of the loop.
			endwhile;
			?>

		</main><!-- .site-main -->

	</div><!-- .content-area -->
<?php
amp_get_footer();
?>
