<?php
global $CONFIG;

$CONFIG = array(
                 'urls' => array('URL_ROOT' => '/'),
                 'plugins' => array('helloworld'),
                 'searchPaths' => array(),
                 'modules'=> array(
                         'session',
                         'router' ,
                     'template' => array('options'=> array('useXHP'=>0)),    
                     'plugins',
                        
                          ),
                );

