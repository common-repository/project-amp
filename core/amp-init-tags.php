<?php
require_once(PROJECTAMP__DIR__."/core/embeds/class-youtube.php");

// register_extension
amp_register_extension("youtube","amp_youtube");

// register_tags
amp_register_tag("img","amp-img",array(),false,'');
amp_register_tag("iframe","amp-youtube",array(),true,'youtube');
