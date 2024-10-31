<?php
global $wp_admin_bar;


add_action( 'amp_footer', 'wp_admin_bar_render', 99 );

amp_insert_css("dashicons",plugins_url("core/assets/css/dashicons.css",dirname(__FILE__)),'font');

add_action("amp-before-custom-css",function(){
  global $wp_admin_bar;
  require_once(ABSPATH."wp-includes/css/admin-bar.min.css");
});
