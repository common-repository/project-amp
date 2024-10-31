<?php
define( 'AMP_DEBUG', false );

add_action("project_amp_theme_setup",function(){
  amp_content_width(600);
});

add_action("amp_enqueue_scripts",function(){

  amp_insert_css("template-style-1",get_amp_template_directory_url()."assets/css/style.css");

});
