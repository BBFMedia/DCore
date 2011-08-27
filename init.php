<?php

  /*** auto load model classes ***/

/**
* array of search paths for autoload
* this array can be added to in any source file as a global
* @global arrray(string) $searchPaths 
*/
define ('__FRAMEWORK_PATH', dirname(__FILE__));
require_once 'DCore.php';



require_once __FRAMEWORK_PATH . 'base.php';

$CONFIG_DEFAULT = array(
                 'urls' => array('URL_ROOT' => '/' ),
                 'paths' => array('__FRAMEWORK_PATH' =>  __FRAMEWORK_PATH),
                 'searchPaths' => array(__PROTECTED_PATH.'/model/',__FRAMEWORK_PATH,__PROTECTED_PATH.'/modules/'),

                                
                 );
  
  
                 
$CONFIG = merge_config($CONFIG_DEFAULT,$CONFIG);
  
define ('URL_ROOT', $CONFIG['urls']['URL_ROOT'] );
define ('URL_THEME', URL_ROOT .'theme/');

 /*** include the controller class ***/


 /*** a new registry object ***/
 $registry = new registry;

 /*** create the database registry object ***/
