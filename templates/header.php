<?php
/**
 * The template for displaying the header
 */
?>
<!doctype html>
<html amp>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
  	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  	<?php endif; ?>
    <link rel="canonical" href="<?php echo get_permalink();  ?>" />
    <?php amp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <nav class='amp-wp-title-bar'>
      <div>
        <a href="<?php echo esc_url( home_url() ); ?>">
    			<?php $site_icon_url = site_icon_url(); ?>
    			<?php if ( $site_icon_url ) : ?>
    				<amp-img src="<?php echo esc_url( $site_icon_url ); ?>" width="32" height="32" class="amp-wp-site-icon"></amp-img>
    			<?php endif; ?>
    			<?php echo esc_html( bloginfo() ); ?>
    		</a>
      </div>
    </nav>
