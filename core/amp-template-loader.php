<?php

function amp_template_part(){
  global $wp_query,$post;

  $out = array();
  $out['theme'] = false;

  if(is_singular()){
    $template = get_post_type_object(get_post_type(get_queried_object()));
  }

  if(is_404() && $template = get_404_template()) :
  elseif(is_search() && $template = get_search_template()) :
  elseif(is_front_page() && $template = get_front_page_template()) :
  elseif(is_home() && $template = get_home_template()) :
  elseif(is_tax() && $template = get_taxonomy_template()) :
  elseif(is_attachment() && $template = get_attachment_template()) :
     remove_filter('the_content', 'prepend_attachment');
  elseif(is_single() && $template = get_single_template()) :
  elseif(is_page() && $template = get_page_template()) :
  elseif(is_category() && $template = get_category_template()) :
  elseif(is_tag() && $template = get_tag_template()) :
  elseif(is_author() && $template = get_author_template()) :
  elseif(is_date() && $template = get_date_template()) :
  elseif(is_archive() && $template = get_archive_template()) :
  elseif(is_comments_popup() && $template = get_comments_popup_template()) :
  elseif(is_paged() && $template = get_paged_template()) :
  else:
    $template = get_index_template();
  endif;

  $dirname = get_template_directory();
  if(is_dir("{$dirname}/amp")){

    // get on theme
    $item = explode("/",$template);
    $last_item = sizeof($item)-1;
    $template = "{$dirname}/amp/$item[$last_item]";

    // fix bug archive
    if(is_archive() && get_post_type() != "post"){
      $template = "{$dirname}/amp/archive-".get_post_type().".php";
    }

    // fix bug singular
    if(is_singular() != "post" || is_singular() != "page"){
      $template = "{$dirname}/amp/single-".get_post_type().".php";
    }

    // support woocommerce
    if(function_exists('is_woocommerce') && is_woocommerce()){

      // remove filters old
      remove_filter("template_include", array('WC_Template_Loader','template_loader'));
      remove_filter("comments_template", array('WC_Template_Loader','comments_template_loader'));

      // fix bug singular
      if(is_singular()){
        $template = "{$dirname}/amp/woocommerce/single-".get_post_type().".php";
      }

      // fix bug archive
      if(is_archive() && get_post_type() != "post"){
        $template = "{$dirname}/amp/woocommerce/archive-".get_post_type().".php";
      }

      // comment template redirect to amp template folder
      add_filter("comments_template", "amp_comments_template",1,1);
      add_filter('wc_get_template_part','amp_woocommerce_template_part' , 1 , 3);

    }

    // support bbpress

    if(function_exists('is_bbpress') && is_bbpress()){

      // future may has single-bbpress.php
      //add_filter( 'bbp_template_include', function($template){
        //echo $template;
        //exit;
      //  return $template;
      //}, 99 );

      add_filter('bbp_get_template_locations','amp_bbpress_change_redirect',1);

    }

    // support buddypress
    if(function_exists('is_buddypress') && is_buddypress()){

      add_filter('bp_get_template_locations','amp_buddypress_change_redirect');
      //bp_get_template_part
      //$templates = apply_filters( 'bp_get_template_part', $templates, $slug, $name );
    }



    // action after encode template dir
    do_action("amp_get_template_part");

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters( 'amp_get_template_part', $template , $item[$last_item] );

    $out['theme'] = true;
    $out['template'] = $template;

  }else{
    // get on plugins
    
    $dirname = PROJECTAMP__DIR__;
    $item = explode("/",$template);
    $last_item = sizeof($item)-1;
    $template = "{$dirname}/templates/$item[$last_item]";

    $out['theme'] = false;
    $out['template'] = $template;

  }
  return $out;
}

function project_amp_render($template){

  require_once($template);
  exit;
}
