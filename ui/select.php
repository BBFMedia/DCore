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
class :ui:select extends :ui:root {
    attribute
    array options,
    :select;
   
  category %flow, %phrase;
  
  function render()
  {
    $options = $this->getAttribute('options');
    $name = $this->getAttribute('name');
    $id = $this->getAttribute('id');
    $class = $this->getAttribute('class');
   $result = 
    <select class={$class} name={$name} id={$id}/>;
    foreach($options as $id => $option)
    {
      
         $item = <option value={$id}>{$option}</option> ;
        if ($id == $selected)
            $item->setAttribute("selected","selected");
    $result->appendChild($item);
            
    }

   return $result;
  }
    
}


