<?php

function amp_head(){
  do_action("amp_head");
}

function amp_footer(){

  //print_r($wp_admin_bar);
  //exit;
  do_action("amp_footer");
}

function amp_get_header($name=""){
  global $amp_path;
  if(is_amp_template()){
    get_template_part("{$amp_path}header",$name);
  }else{
    $item_name = "";
    if(!empty($name)){
      $item_name = "-{$name}";
    }
    require(PROJECTAMP__DIR__."/templates/header{$item_name}.php");
  }
}

function amp_get_footer($name=""){
  global $amp_path;
  if(is_amp_template()){
    get_template_part("{$amp_path}footer",$name);
  }else{
    $item_name = "";
    if(!empty($name)){
      $item_name = "-{$name}";
    }
    require(PROJECTAMP__DIR__."/templates/footer{$item_name}.php");
  }
}

function amp_insert_js($name,$file,$tag){
  global $eq_js;
  if(!isset($eq_js[$name])){
    $eq_js[$name] = array("file"=>$file,"tag"=>$tag);
  }
}

function amp_insert_css($name,$file,$type=""){
  global $eq_css;
  if(!isset($eq_css[$name])){
    $eq_css[$name] = array('url'=>$file,'type'=>$type);
  }
}

function amp_content_width($width) {
  global $project_amp_content_width;
  if(empty($project_amp_content_width)){
    $project_amp_content_width = 600;
  }else{
    $project_amp_content_width = $width;
  }
}

function amp_run_script(){
  global $eq_js , $eq_css , $wp_filesystem,$project_amp_content_width;
  require_once (ABSPATH . '/wp-admin/includes/file.php');
  WP_Filesystem();
  do_action("amp_before_action_script");
  ob_start();
  if(is_array($eq_css) && sizeof($eq_css) > 0){
    foreach($eq_css as $files){
      $item = array();
      $file = $files['url'];
      if($files['type'] != "font"){

        preg_match('/wp-content\/(\S+)/i', $file, $item);

        if(isset($item[0]) && !empty($item[0])){
          $item_file = ABSPATH.$item[0];
        }else{
          $item_file = "";
        }

        if(!empty($item_file) && is_file($item_file)){

          require($item_file);
        }else{
          $file_explode = explode("/",$file);
          $count = sizeof($file_explode) - 1;
          if(empty($file_explode[$count])){
            continue;
          }
          $file_name = $file_explode[$count];
          $folder_cache = get_amp_template_directory()."cache-css";

          if(is_file($folder_cache."/".$file_name)){
            require_once($folder_cache."/".$file_name);
          }else{
            $text = $wp_filesystem->get_contents($file);
            if(is_dir($folder_cache)){
              $wp_filesystem->put_contents($folder_cache."/".$file_name,$text);
            }else{
              wp_mkdir_p($folder_cache);
            }
            if(!empty($text)){
              echo $text;
            }
          }
        }
      }

    }
    $out = ob_get_clean();
    $out = str_replace("{{amp_template}}",get_amp_template_directory_url(),$out);
    $out = str_replace("{{amp_content_width}}",$project_amp_content_width."px",$out);
    $out = do_action("amp_create_tag");
    $out = apply_filters("amp-css",$out);
    $out = compress($out);
    echo "<style amp-custom>";
    do_action("amp-before-custom-css",$out);
    echo apply_filters("amp-css-action",$out);
    do_action("amp-after-custom-css",$out);
    echo "</style>";

    foreach($eq_css as $files){
      if($files['type'] == "font"){
        echo "<link href='{$files['url']}' rel='stylesheet' type='text/css'>";
      }
    }

    foreach($eq_css as $files){
      if($files['type'] == "style"){
        echo "<link href='{$files['url']}' rel='stylesheet' type='text/css'>";
      }
    }
  }

  if(is_array($eq_js) && sizeof($eq_js) > 0){
    foreach($eq_js as $files){
        echo "<script async custom-element='{$files['tag']}' src='{$files['file']}'></script>";
    }
  }


  do_action("amp_after_action_script");
  if(is_user_logged_in()) {
    echo '<style type="text/css" media="screen"> html { margin-top: 32px !important; } * html body { margin-top: 32px !important; } @media screen and ( max-width: 782px ) { html { margin-top: 46px !important; } * html body { margin-top: 46px !important; } } </style>';
  }

  $script_amp_project = apply_filters("script_amp_project",plugins_url("core/assets/js/v0.js",dirname(__FILE__)));
  echo '<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
  <script async src="'.$script_amp_project.'"></script>';
  do_action("amp_after_main_script");

}

function amp_create_tag($args){
  global $amp_create_tag_name,$amp_create_tag_value;
  $amp_create_tag = array();
  $amp_create_tag_value = array();
  if(is_array($args)){
    foreach($args as $name){
      if(isset($name['name']) && isset($name['value']) && !empty($name['name'])){
        $amp_create_tag_name[] = "{{".$name['name']."}}";
        $amp_create_tag_value[] = $name['value'];
      }else{
        continue;
      }
    }
    if(sizeof($amp_create_tag) > 0){
      add_filter("amp-css","__amp_create_tag_action");
    }
  }
  return;
}

function __amp_create_tag_action($css){
  global $amp_create_tag_name,$amp_create_tag_value;
  $out = str_replace($amp_create_tag_name,$amp_create_tag_value,$css);
  return $out;
}

function amp_get_sidebar($name=""){
  global $amp_path;
  if(is_amp_template()){
    get_template_part("{$amp_path}sidebar",$name);
  }else{
    $item_name = "";
    if(!empty($name)){
      $item_name = "-{$name}";
    }
    require(PROJECTAMP__DIR__."/templates/sidebar{$item_name}.php");
  }
}

function amp_get_template_part($slug,$name="") {
  global $amp_path;
  if(is_amp_template()){
    get_template_part("{$amp_path}{$slug}",$name);
  }else{
    $item_name = "";
    if(!empty($name)){
      $item_name = "-{$name}";
    }
    require(PROJECTAMP__DIR__."/templates/{$slug}{$item_name}.php");
  }
}

function is_amp(){
  if(amp_check_mobile()){
    return true;
  }
  return false;
}

function compress($buffer) {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
}

function amp_register_tag($old_tag,$new_tag,$attr=array(),$extension=false,$name_extension=""){
  global $amp_tags;
  $amp_tags->register_tags($old_tag,$new_tag,$attr,$extension,$name_extension);
}

function amp_register_extension($name,$extension){
  global $amp_tags;
  $amp_tags->register_extension($name,$extension);
}
