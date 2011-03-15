<?php

 $CONFIG = merge_config(array('plugins' => array()),$CONFIG);

 class plugins extends baseClass
 {
  
  function loadPlugins()
  {
  global $CONFIG;
  foreach ($CONFIG['plugins'] as $plugin)
     {
      require_once __PROTECTED_PATH .'/plugins/'. $plugin .'/init.php';
     }
  }
 
 }

?>
