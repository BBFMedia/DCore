<?php

/***********

Template is in charge of rendering views

An nested view system is used. Meaning that a masterpage is rendered and 
is responisble for rendering all views with in masterpage and those view are 
responisble for the view with in them and so on.


Calling render($viewname) will return the rendered string of that view and that views nested views.


**********************
registering a view

setView('viewname','pluginname:viewfile');
   would register 
       protected/plugins/{pluginname}/views/default/{viewfile}.php
    as a view for "viewname"
    
    if pluginname is left off then use the path
       protected/views/{viewfile}.php
        
   
   note that a folder named "default" is in the path. This is the view type.
     the view type can be set by calling setViewType($viewType)
     view type "default" is default
     template would look in 
       protected/plugins/{pluginname}/views/{viewtype}/{viewfile}.php
     if it does not exist then look in 
       protected/plugins/{pluginname}/views/default/{viewfile}.php

 can also call setView('viewname','pluginname:viewfile','viewtype');
 
 example 
    $this->registry->templates->setView('viewname','pluginname:viewfile','mobile');
    
    if the view type is set to "mobile" then it would render the this file as as
    instead of the file registered else where. 
    


**********************
rendering a view  
   when calling render the class will first check if the view is registered if so renders that file
   if the view is not registered it will look for the view as
         protected/views/{viewname}.php 
  
  
  Plugins may override controllers with this scheama. So the order of plugin initalization can effect this.
  
 
standard practice would be the master template would be registered as "masterpage"
and the content be registered as "contents"
plus in master controller should be <?php $this->render('contents') ?>


templates has a helper function show($viewname) to set the "contents" view. usually
the controller would call this to set its view



**********/

Class Template extends baseClass
{


/*
 * @Variables array
 * @access private
 */
private $view_type = 'default'; 
private $vars = array();
private $views = array();

/**
 *
 * @constructor
 *
 * @access public
 *
 * @return void
 *
 */



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
  public function __get($index)
 {
       return $this->vars[$index] ;
 }
 public function __set($index, $value)
 {
        $this->vars[$index] = $value;
 }
 function setViewType($viewtype)
 {
  $this->view_type =  $viewtype;
 }
 function setView($view,$view_path,$view_type = 'default')
 {
 $this->views[$view_type][$view] = $view_path; 
 }
function render($name)
 {
        $view_root =  __PROTECTED_PATH ;
        $view_info = explode(':',$this->views[$this->view_type][$name]);
        if (count($view_info) > 1)
            $view_root =  __PROTECTED_PATH .'plugins/'. $view_info[0];
            else
            $view_info[1] = $view_info[0];
            
	$path = $view_root . '/views' . '/' .$this->view_type.'/'. $view_info[1] . '.php';
	if (file_exists($path) == false)
        	$path = $view_root . '/views/default/'. $view_info[1] . '.php';

	if (file_exists($path) == false)
        	$path = __PROTECTED_PATH . '/views' . '/' . $this->views['default'][$name] . '.php';

	if (file_exists($path) == false)
	{
		throw new Exception('Template not found in '. $path);
		return false;
	}

	// Load variables
	foreach ($this->vars as $key => $value)
	{
		$$key = $value;
	}

        //use the buffer to send out the masterpage
        ob_start();
        include ($path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;

          
}
function show($name)
{
  $this->setView('contents',$name);
   
}

}

?>