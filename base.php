<?php
/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: Mar 11, 2011
 * Time: 4:18:23 PM
 * To change this template use File | Settings | File Templates.
 */

require_once __FRAMEWORK_PATH . 'baseClass.class.php';
require_once __FRAMEWORK_PATH . 'controller_base.class.php';
function addSearchPath($path)
{
 global $CONFIG;
 $paths = explode(':',$path);
 if (count($paths) > 1)
 {
   $paths[0] = __PROTECTED_PATH.'plugins/'.$paths[0].'/';
     $path = $paths[0].$paths[1];
 }
 $CONFIG['searchPaths'][] = realpath($path);
}

function get_file_path($filename,$type = '',$view_type='default')
{
        $view_info = explode(':',$filename);
        if (count($view_info) > 1)
         {
        $plugpath = __FRAMEWORK_PATH .'core/'. $view_info[0].'/';
        if (!file_exists($plugpath))
            $plugpath = __PROTECTED_PATH .'plugins/'. $view_info[0].'/';

        if ($type== 'views')
        {
            $file =  $plugpath.'/'.$type.'/'.$view_type.'/'.$view_info[1].'.php';
            if (!file_exists($file))
                 $file =  $plugpath.'/'.$type.'/default/'.$view_info[1].'.php';
            if (!file_exists($file))
                 $file =  $plugpath.'/'.$type.'/'.$view_info[1].'.php';

         if (file_exists($file))
          return $file;

        }
        else
        {
           $file =  $plugpath.$type.'/'.$view_info[1].'.php';

           if (!file_exists($file))
                $file =  __PROTECTED_PATH .$type.'/'. $view_info[0].'.php';

           if (file_exists($file))
             return $file;
         }
        }
        else
        {
              $file = __PROTECTED_PATH .$type.'/'. $filename.'.php';

           if (file_exists($file))
             return $file;

        }
    	if (file_exists($file) == false)
	{
		throw new Exception('Template not found in '. $filename);
		return false;
	}

}
    /**
}
* Autoload for classes
* @global __autoload  Enums $searchPaths to find a matching filename and includes it
* @param string $class_name classname
* @return boolean
*/
function __autoload($class_name) {
    $file = can_auto_load($class_name);
           if (file_exists($file))
             {
              require_once ($file);
              return true;
             }

      return false;
}
function can_auto_load($class_name)
{
      global $CONFIG;
      //create filename from classname
      $filename = $class_name . '.class.php';
      $filename2 = $class_name . '.php';

    // emum   $searchPaths
      foreach ( $CONFIG['searchPaths'] as $path)
        {
          $file =  rtrim($path,'/\\'). '/'.$filename;
       //include class source file if found
           if (file_exists($file))
             {
              return ($file);

             }
          $file =  rtrim($path,'/\\').'/'. $filename2;
       //include class source file if found
           if (file_exists($file))
             {
              return ($file);
             }
         }
      return false;


}

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


