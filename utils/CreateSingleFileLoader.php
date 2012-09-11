<?php

$content = '<?php ';

$sources = array(
    'base',
   'baseClass.class',
   'DCore',
   'registry.class',
    'template.class',
 'router.class',
 'session.class',
  'plugins.class',
  'assetManager.class',
  'cache.class',
 'controller_base.class',
 'init');

foreach($sources as $source)
{
$data = file_get_contents($source.'.php');

$data = substr($data,5 ,strlen($data));
$data = preg_replace('!/\*.*?\*/!s', '', $data);
$data = preg_replace('/\n\s*\n/', "\n", $data);
$data = preg_replace("/\nrequire_once/", "\n//-require_once",$data);

$content .= $data;
}

file_put_contents('DFull.php',$content);
