<?php

 global $registry;

   define ('__ROOT_PATH',dirname(__FILE__).'/' );
 define ('__PROTECTED_PATH', dirname(__FILE__) .'/protected/');
 define ('__DCORE', dirname(dirname(dirname(__FILE__))) .'/');
 include __DCORE.'/helpers/xhprofile.php';

  
 include __DCORE. 'init.php';

  
  
 $registry->load();


 $registry->router->loader();
 echo $registry->template->render('helloworld:layout');

