<?php

// function convert dir kinda woocommerce singluar
function amp_woocommerce_template_part($template,$slug,$name){
  if(empty($name)){
    $template = get_amp_template_directory() . "woocommerce/{$slug}-{$name}.php";
  }else{
    $template = get_amp_template_directory() . "woocommerce/{$slug}.php";
  }
  return $template;
}

// function convert dir kinda woocommerce comments
function comments_template_loader( $template ) {
		if ( get_post_type() !== 'product' ) {
			return $template;
		}

		$check_dirs = array(
			trailingslashit( get_stylesheet_directory() ) . get_amp_template_directory()."/amp/woocommerce/",
			trailingslashit( get_template_directory() ) . get_amp_template_directory()."/amp/woocommerce/",
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( WC()->plugin_path() ) . 'templates/'
		);

		if ( WC_TEMPLATE_DEBUG_MODE ) {
			$check_dirs = array( array_pop( $check_dirs ) );
		}

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'single-product-reviews.php' ) ) {
				return trailingslashit( $dir ) . 'single-product-reviews.php';
			}
		}
	}
