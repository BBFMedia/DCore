<?php

/**
 * Most classes inherit from baseClass 
 *
 * @version $Id$
 * @copyright 2009
 * @package DCore/core
 */
class baseClass {

    /**
     * @var  Registry 
     */
    public $registry;

    public function init() {
        
    }

    function __construct($registry, $options = null) {
        $this->registry = $registry;
    }

}

