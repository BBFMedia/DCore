<?php

 $CONFIG = merge_config(array('plugins' => array()),$CONFIG);
 
 /**
  *  
  */
 class plugin extends baseClass
 {
  public $path = '';
  public $name = '';
  
   function __construct($plugin,$path,$registry,$options = null) {
       parent::__construct( $registry,$options);
 $this->path = rtrim($path,'/');
 $this->name = $plugin;
  
 }

  function init()
  {
   $registry = $this->registry;
   $plugin = $this;
   include( rtrim($this->path,'/') . '/init.php');
  }
 
 }
                                                   
 class plugins extends baseClass
 {
 private $_pluginPaths = array();
 private $_loadedPlugins = array();
 
 function init()
 {       
  $this->loadPlugins();
 }
 
 function _createPluginClass($plugin,$plugpath)
   {
     $pluginclass = 'plugin';
    if (file_exists( rtrim($plugpath).'/'.$plugin.'Plugin.php'))
           {
  
           require_once( rtrim($plugpath).'/'.$plugin.'Plugin.php')  ;
             $pluginclass =   $plugin.'Plugin';
           } 
    $p = new $pluginclass($plugin,$plugpath,$this->registry);
  
    return $p;
   }
  function loadPlugin($plugin )
  {
  global $CONFIG,$registry;
  
  foreach($this->pluginPath as $path )
    {
   
      $plugpath =  $path.$plugin;      
      $init =   $plugpath .'/init.php';
      $pl =   $plugpath .'/'.$plugin.'Plugin.php';
      if (file_exists($init) or (file_exists($pl)))
       {
        DCore::setPathOfAlias($plugin,$plugpath);
        $pluginclass = $this->_createPluginClass($plugin,$plugpath);
        $pluginclass->init();
        $this->_loadedPlugins[$plugin] = $pluginclass;
       return true;
        }
     }
     die('plugin '. $plugin.' not found');
  }
  function addPluginDirectory($name, $path)
  {
    $this->_pluginPaths[$name] = $path;
  }
  function requiredPlugins($plugs)
    {
    foreach($plugs as $plug)
    {
     if (empty($this->_loadedPlugins[$plug]))
       {
        echo('plugin '.$plug->name.' required');
        var_dump($plugs);
        die(); 
       }
    }
    }
  function loadPlugins()
  {
  global $CONFIG;
  if (empty($this->pluginPath))
      $this->pluginPath =  $CONFIG['pluginPath'];
  if (empty($this->pluginPath))
   {
    $this->pluginPath[]  = __PROTECTED_PATH .'plugins/';
    $this->pluginPath[]  = __FRAMEWORK_PATH .'core/';
    }
    
  foreach ($CONFIG['plugins'] as $plugin)
     {
     $this->loadPlugin($plugin );
     }
    
  }
 
 }
