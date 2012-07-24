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
abstract class :ui:root extends :x:element {
    //put your code here
    public $registry;
    function init(){
        global $registry;
        $this->registry = $registry;
        parent::init();
    }
    
}


