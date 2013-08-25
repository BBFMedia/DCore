<?php
global $CONFIG;

$CONFIG = array(
                 'urls' => array('URL_ROOT' => '/'),
                 'plugins' => array('helloworld'),
                 'searchPaths' => array(),
                 'modules'=> array(
                         'cache' => array('class'=>'cache\apcCache','options'=>array('scope' => array('org_id'=>5 ) ,'DCoreCache'=>1)),
                         'session',
                         'router' ,
                     'template' => array('options'=> array('useXHP'=>0)),    
                     'plugins',
                        
                          ),
                );

DCore::setPathOfAlias('twig',realpath(dirname(__FILE__) . '/../../../../../Twig/lib/Twig'));