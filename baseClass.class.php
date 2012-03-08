<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2009
 */

class baseClass 
{


/**
 * @var registry
 */
 public $registry;

 public function init(){}

 function __construct($registry,$options = null) {
        $this->registry = $registry;
 }


}

