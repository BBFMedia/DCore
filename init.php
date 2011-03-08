<?php

  /*** auto load model classes ***/

/**
* array of search paths for autoload
* this array can be added to in any source file as a global
* @global arrray(string) $searchPaths 
*/
function merge_config_item($core,$config)
{
  if (empty($core) )
    return $config;
  if (empty($config) )
    return $core;
  return  array_merge($core,$config);
}
function merge_config($core,$config)
{
foreach ($core as $key => $item) 
{

     $config[$key] = merge_config_item($core[$key],$config[$key]);
}
return $config;    
}

$CONFIG_DEFAULT = array(
                 'urls' => array('URL_ROOT' => '/up/' ),
                 'paths' => array('__FRAMEWORK_PATH' =>  'application/'),
                 'searchPaths' => array(__PROTECTED_PATH.'/model/',__PROTECTED_PATH.'/application/',__PROTECTED_PATH.'/modules/'),

                                
                 );
  
  
                 
$CONFIG = merge_config($CONFIG_DEFAULT,$CONFIG);
  
define ('URL_ROOT', $CONFIG['urls']['URL_ROOT'] );
define ('URL_THEME', URL_ROOT .'theme/');
define ('__FRAMEWORK_PATH', __PROTECTED_PATH . $CONFIG['paths']['__FRAMEWORK_PATH']);


/**
* Autoload for classes
* @global __autoload  Enums $searchPaths to find a matching filename and includes it
* @param string $class_name classname
* @return boolean 
*/
function __autoload($class_name) {
      global $CONFIG;
      //create filename from classname
    $filename = strtolower($class_name) . '.class.php';
    
    // emum   $searchPaths
      foreach ( $CONFIG['searchPaths'] as $path)
        {
       $file =  $path. $filename;
    //include class source file if found
        if (file_exists($file))
          {
           include ($file);   
           return true;
          }
         }
      return false;  
    

}
 /*** include the controller class ***/
 include __FRAMEWORK_PATH . 'controller_base.class.php';






 /*** a new registry object ***/
 $registry = new registry;

 /*** create the database registry object ***/
 $registry->db = db::getInstance();




 ?>
