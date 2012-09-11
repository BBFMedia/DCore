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
abstract class :ui:element extends :xhp:html-element{
   
     public $registry;
    function init(){
        global $registry;
        $this->registry = $registry;
        parent::init();
    }
}

