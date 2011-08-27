<?php

 $CONFIG = merge_config(array('plugins' => array()),$CONFIG);

 class plugins extends baseClass
 {
 private $_pluginPaths = array();
 private $_loadedPlugins = array();
  function loadPlugin($plugin )
  {
  global $CONFIG,$registry;
  
  foreach($this->pluginPath as $path )
    {
      $plugpath =  $path.$plugin;             
      $init =   $plugpath .'/init.php';
      if (file_exists($init))
       {
        DCore::setPathOfAlias($plugin,$plugpath);
        require_once $init ;
        $this->_loadedPlugins[$plugin] = $plugpath;
       break;
      }
     }
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
        echo('plugin '.$plug.' required');
        var_dump($plugs);
        die(); 
       }
    }
    }
  function loadPlugins()
  {
  global $CONFIG;
  if (empty($this->pluginPath))
    $this->pluginPath[]  = __PROTECTED_PATH .'plugins/';
  foreach ($CONFIG['plugins'] as $plugin)
     {
     $this->loadPlugin($plugin );
     }
  }
 
 }
