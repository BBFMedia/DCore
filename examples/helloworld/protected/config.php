<?php
global

$CONFIG = array(
                 'urls' => array('URL_ROOT' => '/'),
                 'plugins' => array('helloworld'),
                 'searchPaths' => array(__DRIGHT_PATH,__DIMPLE_PATH,__PROTECTED_PATH .'../../clientado/core/models/'),
                 'modules'=> array(
                         'session',
                         'router' ,
                         'plugins',
                          ),
                );

