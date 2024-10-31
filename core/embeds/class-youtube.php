<?php
class amp_youtube extends amp_tags {


  public static function init(){
    $src = apply_filters("amp_youtube_js",plugins_url("embeds/assets/js/amp-youtube-0.1.js",dirname(__FILE__)));
    amp_insert_js("youtube",$src,"amp-youtube");
    return new amp_youtube();
  }

  public function render($attr){ //

    if(strpos($attr['src'],'youtube')){
      $match = array();
      if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $attr['src'], $match)) {
        $video_id = $match[1];
      }

      return "
      <amp-youtube
      data-videoid='{$video_id}'
      layout='responsive'
      width='{$attr['width']}'
      height='{$attr['height']}'></amp-youtube>";
    }

  }

}
