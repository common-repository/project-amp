<?php

class amp_tags {

  public $tags = array();
  public $extensions = array();
  public $use_extensions = array();

  public function __construct(){

  }

  public function register_extension($name,$object){
    $this->extensions[$name] = array('name'=>$name,'object'=>$object);
  }

  public function register_tags($old_tag,$new_tag,$conditions,$extension,$name_extension){
    $this->tags[] = array('name'=>$old_tag,'old'=>$old_tag,'new'=>$new_tag,'conditions'=>$conditions,'extension'=>$extension,'name_extension'=>$name_extension);
  }

  public function process_dom(){
    foreach($this->extensions as $name=>$extension){
      $this->use_extensions[$name] = call_user_func(array('amp_'.$name,'init'));
    }
    add_filter("the_content",array($this,'render_tags'));
  }

  public function render_tags($content){
    if(sizeof($this->tags) > 0){
      foreach($this->tags as $tag){
        if(!$tag['extension']){
          $content = $this->renameTags($content,$tag['old'],$tag['new']);
        }else{
          foreach($this->use_extensions as $key => $extension){
              $content = $this->excute_extension($content,$tag['old'],$tag['new'],$this->use_extensions[$key],$tag['conditions']);

            //$content
          }
        }
      }
    }
    return $content;
  }

  private function dom_rename_element(DOMElement $node, $name) {
    $renamed = $node->ownerDocument->createElement($name);

    foreach ($node->attributes as $attribute) {
        $renamed->setAttribute($attribute->nodeName, $attribute->nodeValue);
    }

    while ($node->firstChild) {
        $renamed->appendChild($node->firstChild);
    }

    return $node->parentNode->replaceChild($renamed, $node);
  }

  private function excute_extension($html, $oldname, $name, $extension, $conditions) {
    $dom = new DOMDocument( '1.0', 'utf-8' );
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    $xp = new DOMXPath($dom);
    if(isset($conditions['class'])){
      $oldNode = $xp->query('//'.$oldname.'[contains(@class,"'.$conditions['class'].'")]');
    }else{
      $oldNode = $xp->query('//'.$oldname);
    }
    foreach ($oldNode as $tag){
      $attr = array();
      $attr['pamp-tagname'] = $oldname;
      $temp_dom = new DOMDocument();
      $temp_dom->appendChild($temp_dom->importNode($tag,true));
      $attr['pamp-fulltext'] = $temp_dom->saveHTML();
      $attr['pamp-DOMChildnodes'] = $tag->childNodes;
      foreach ($tag->attributes as $attribute) {
          $attr[$attribute->nodeName] = $attribute->nodeValue;
      }
      $text = $extension->render($attr);
      $item = $dom->createDocumentFragment();
      $item->appendXML($text);
      $tag->parentNode->replaceChild($item, $tag);
      //$tag = $this->dom_rename_element($tag,$name);
    }
    return preg_replace('~<(?:!DOCTYPE|/?(?:html|body|head))[^>]*>\s*~i', '', $dom->saveHTML());
  }

  private function renameTags($html, $oldname, $name) {
    $dom = new DOMDocument( '1.0', 'utf-8' );
    $dom->loadHTML($html);
    $xp = new DOMXPath($dom);
    $oldNode = $xp->query('//'.$oldname);
    foreach ($oldNode as $tag){
      $tag = $this->dom_rename_element($tag,$name);

    }
    return preg_replace('~<(?:!DOCTYPE|/?(?:html|body|head))[^>]*>\s*~i', '', $dom->saveHTML());
  }

}
