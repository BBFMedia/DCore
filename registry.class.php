<?php

Class Registry {

 /*
 * @the vars array
 * @access private
 */
 private $vars = array();

/**
 * @var template
 */
 public $template;
/**
 * @var router
 */
public $router;

 /**
 *
 * @set undefined vars
 *
 * @param string $index
 *
 * @param mixed $value
 *
 * @return void
 *
 */
 public function __set($index, $value)
 {
	$this->vars[$index] = $value;
 }

 /**
 *
 * @get variables
 *
 * @param mixed $index
 *
 * @return mixed
 *
 */
 public function __get($index)
 {
	return $this->vars[$index];
 }

 public function load()
 {                                                                    
  global $CONFIG;

  $mods = $CONFIG['modules'];   
  foreach($mods as $key => $item)
  {
   
   if (!is_array($item))
     {
       $key = $item;
      }
   $cf = array('class' => $key , 'alias' => $key );
   if (is_array($item))
     {
       $cf  = array_merge($cf  , $item);
      }
     
   $alias = $cf['alias'];
   $class = $cf['class'];
   $options = $cf['options'];

   $this->$alias = new  $class($this,$options); 
   $this->$alias->init();   
      
  }
 
if( file_exists(__PROTECTED_PATH. 'init.php'))  
include __PROTECTED_PATH. 'init.php';

 }
}
