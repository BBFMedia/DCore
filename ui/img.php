<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of root
 *
 * @author Adrian
 */
class :ui:img extends :ui:element {
    attribute 
    :img;
   
  category %flow, %phrase;
  protected $tagName = 'img';
   function stringify()
   {
    //  return <span/>;
       $src = $this->getAttribute('src');
       $src = $this->registry->assetManager->publishFilePath($src);
       $this->setAttribute('src',$src);
       return parent::stringify();
   }
    
}


