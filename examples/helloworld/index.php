<?php

 global $registry;

  
 define ('__PROTECTED_PATH', dirname(__FILE__) .'/protected/');
  
  
 include __FRAMEWORK_PATH. 'init.php';

  
  
 $registry->load();


 $registry->router->loader();

