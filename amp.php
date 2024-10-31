<?php
/**
 * Plugin Name: PROJECT AMP
 * Description: AMP Support on Theme
 * Plugin URI: https://github.com/Ba5nanas/project-amp/
 * Author: Ba5nanas
 * Author URI: http://themeforest.net/user/ba5nanas
 * Version: 0.2.2
 * Text Domain: project amp
 * Domain Path: /languages/
 * License: GPLv2 or later
 */

define( 'PROJECTAMP__FILE__', __FILE__ );
define( 'PROJECTAMP__DIR__', dirname( __FILE__ ) );

register_activation_hook( __FILE__, 'project_amp_activate' );
function project_amp_activate(){
	amp_init_first();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'project_amp_deactivate' );
function project_amp_deactivate(){
	flush_rewrite_rules();
}

// load includes
add_action('init','amp_init_first',2);
function amp_init_first(){
  global $amp_path;
  do_action('before_amp_load_library');
	if(function_exists("amp_init")){
		return;
	}
	if(strpos($_SERVER['HTTP_USER_AGENT'],"Google")){
    return;
  }
  $fban = apply_filters("amp-instant-article",false);
  if(strpos($_SERVER['HTTP_USER_AGENT'],"FBAN") && $fban == true){
    return;
  }
  $amp_path = apply_filters("get_amp_template_directory_name","amp/");
  add_action( 'amp_load_library' , 'amp_load_library' );
  add_action( 'amp_load_module' , 'amp_load_module' );
  add_action( 'amp_load_helpers' , 'amp_load_helpers' );
  add_action( 'amp_load_init' , 'amp_load_init' );

  // load function
  if(is_amp_template()){
    $dirname_amp = get_amp_template_directory();
    if(is_file("{$dirname_amp}functions.php")){
      do_action('before_amp_load_functions');
      // load functions.php on folder theme
      include_once("{$dirname_amp}functions.php");
    }
  }else{
    do_action('before_amp_load_functions');
    include_once(PROJECTAMP__DIR__."/templates/functions.php");
  }

  // init system
  do_action("before_project_amp_init");
  add_action( 'init', 'project_amp_init' , 99);
  do_action("after_project_amp_init");
}

function is_amp_template(){
  global $amp_path;
  $dirname = get_template_directory();
  $name = get_template_directory().$amp_path;

  if(is_dir($name)){
    return true;
  }else{
    return false;
  }
}

function project_amp_init(){
  global $amp_tags;
  // load Library
  do_action('amp_load_library');
  $amp_tags = new amp_tags();
  do_action('amp_load_module');
  do_action('amp_load_helpers');
  do_action('amp_load_init');
	do_action("project_amp_theme_setup");


  // amp add script
  add_action( 'amp_head' , 'amp_run_script' );
  // amp action

  add_action( 'template_redirect', 'amp_action' );
}

function get_amp_template_directory(){
  global $amp_path;
  $dirname = get_template_directory();
  $name = get_template_directory()."/".$amp_path;
  $dir = apply_filters("get_amp_template_directory",$name);
  if(is_dir($dir)){
    // get folder on theme
    return $dir;
  }else{
    // get folder on plugin
    return PROJECTAMP__DIR__."/templates/";
  }
}

function get_amp_template_directory_url(){
  global $amp_path;
  $dirname = get_template_directory_uri();
  $name = get_template_directory_uri()."/".$amp_path;
  $dir = apply_filters("get_amp_template_directory_url",$name);
  if(is_amp_template()){
    return $dir;
  }else{

    return plugins_url("templates/",__FILE__);
  }
}

function amp_action(){
  global $amp_tags;

  $mytheme = wp_get_theme();
  $amp_template = amp_template_part();

  if(amp_check_mobile() && is_file($amp_template['template'])){
    do_action("amp_enqueue_scripts");
    require_once(PROJECTAMP__DIR__."/core/amp-templates-actions.php");
    $amp_tags->process_dom();
    // render
    project_amp_render($amp_template['template']);
  }

}

// check mobile working
function amp_check_mobile(){
  global $detect;
  if (defined('AMP_DEBUG') && AMP_DEBUG == true) {
    return true;
  }
  if($detect->isMobile() || $detect->isTablet() || ($detect->isMobile() && !$detect->isTablet()) ){
    return true;
  }
}

function amp_load_library(){
  global $detect;
  require_once(PROJECTAMP__DIR__."/core/libs/Mobile_Detect.php");
  require_once(PROJECTAMP__DIR__."/core/class-amp.php");
  require_once(PROJECTAMP__DIR__."/core/class-amp-tags.php");
  $detect = new Mobile_Detect;
  do_action("on_amp_load_library");
}

function amp_load_module(){
  require_once(PROJECTAMP__DIR__."/core/amp-woocommerce.php");
  require_once(PROJECTAMP__DIR__."/core/amp-bbpress.php");
  require_once(PROJECTAMP__DIR__."/core/amp-buddypress.php");
  do_action("on_amp_load_module");
}

function amp_load_helpers(){

  require_once(PROJECTAMP__DIR__."/core/amp-helpers.php");
  require_once(PROJECTAMP__DIR__."/core/amp-template-loader.php");

  do_action("on_amp_load_helpers");
}

function amp_load_init(){

  require_once(PROJECTAMP__DIR__."/core/amp-init-tags.php");
  do_action("on_amp_load_init");

}
