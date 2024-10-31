<?php
/**
 * The template used for displaying page content
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>


<article id="post-<?php the_ID(); ?>" <?php post_class(""); ?>>

	<header class="entry-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	</header><!-- .entry-header -->

	<ul class="amp-wp-meta">

		<li class="amp-wp-byline">
			<?php if ( function_exists( 'get_avatar_url' ) ) : ?>
			<amp-img src="<?php echo esc_url( get_avatar_url( get_the_author_meta('user_email'), array(
				'size' => 24,
			) ) ); ?>" width="24" height="24" layout="fixed"></amp-img>
			<?php endif; ?>
			<span class="amp-wp-author"><?php echo esc_html( get_the_author_meta('display_name') ); ?></span>
		</li>

		<li class="amp-wp-posted-on">
			<time datetime="<?php echo esc_attr( date( 'c', get_the_time('U') ) ); ?>">
				<?php
				echo esc_html(
					sprintf(
						_x( '%s ago', '%s = human-readable time difference', 'amp' ),
						human_time_diff( get_the_time('U') )
					)
				);
				?>
			</time>
		</li>

		<?php $categories = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'amp' ) ); ?>
		<?php if ( $categories ) : ?>
			<li class="amp-wp-tax-category">
				<span class="screen-reader-text">Categories:</span>
				<?php echo $categories; ?>
			</li>
		<?php endif; ?>

		<?php $tags = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'amp' ) ); ?>
		<?php if ( $tags ) : ?>
			<li class="amp-wp-tax-tag">
				<span class="screen-reader-text">Tags:</span>
				<?php echo $tags; ?>
			</li>
		<?php endif; ?>

	</ul>

	<div class="entry-content">
		<?php the_content(); ?>
	</div>

</article><!-- #post-## -->
