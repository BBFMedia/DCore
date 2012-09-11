<?php

/**
 * abstract class all controllers are extended from including rest controllers
 * 
 * @package DCore/core
 */
Abstract Class baseController extends baseClass {

    /**
     * @all controllers must contain an index method
     */
    abstract function index();
}

