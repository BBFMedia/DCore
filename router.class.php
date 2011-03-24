<?php

/*

 router dispatches http request
 1) determine which controller to use
 2) determine action to be called on controller
 3) add url params to template   name/value/name/value/name/value


 router will first search the registered controllers and then look in the default
 controller path  for a file "controlname.class.php"
 
 
  ***********
  REGISTER CONTROLLER
  
  calling setController will set or overide if exists the the controller class.
  
  $this->registry->router->setController('controllerName','pluginname:controllerFilename');
  
  if "controllerName" is requested the router would look in 
       protected/plugins/{pluginname}/controller/{controllerFilename}Controller.php
 
  
  Plugins may override controllers with this scheama. So the order of plugin initalization can effect this.
  
 
*/


class router extends baseClass {


    /*
    * @the controller path
    */
    private $path;

    private $args = array();

    private $controllers = array();

    public $file;

    public $controller;


    public $action;

    public $rewriteOn = true;

    /**
     *
     * @set controller directory path
     *
     * @param string $path
     *
     * @return void
     *
     */
    function setPath($path) {

        /*** check if path i sa directory ***/
        if (is_dir($path) == false) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        /*** set the path ***/
        $this->path = $path;
    }

    function setController($controllerName, $controllerLocal) {
        $this->Controllers[$controllerName] = $controllerLocal;

    }

    /**
     *
     * @load the controller
     *
     * @access public
     *
     * @return void
     *
     */
    public function loader() {
        /*** check the route ***/
        $this->getController();

        /*** if the file is not there diaf ***/
        if (is_readable($this->file) == false) {
            $this->file = $this->path . '/error404.php';
            $this->controller = 'error404';
        }

        /*** include the controller ***/
        include $this->file;

        /*** a new controller class instance ***/
        $class = $this->controller . 'Controller';
        $controller = new $class($this->registry);

        /*** check if the action is callable ***/
        if (is_callable(array($controller, $this->action . 'Action'))) {
            $action = $this->action . 'Action';
        }
          else
            {
                $action = 'index';

            }
        /*** run the action ***/

        $controller->$action();
    }


    public function redirect($url) {
        header("Location: " . $url);
        die;

    }

    /**
     *
     * @create a url to call a controller
     *
     *
     *
     * @return string
     *
     */
    public function buildUrl($controller, $action = 'index', $params = array()) {

        $url = $controller . '/' . $action;

        $p = '';


        if ($this->rewriteOn) {
            foreach ($params as $key => $param)
            {
                $p .= '/' . $key . '/' . $param;
            }
            $result = URL_ROOT . $url . $p;
        }
        else
        {
            foreach ($params as $key => $param)
            {
                $p .= '&' . $key . '=' . $param;
            }
            $result = URL_ROOT . '?rt=' . $url . $p;
        }
        return $result;
    }

    /**
     *
     * @get the controller
     *
     * @access private
     *
     * @return void
     *
     */
    private function getController() {

        /*** get the route from the url ***/
        $route = (empty($_GET['rt'])) ? '' : $_GET['rt'];

        if (empty($route)) {
            $route = 'index';
        }
        else
        {
            /*** get the parts of the route ***/
            $parts = explode('/', $route);
            $this->controller = $parts[0];
            if (isset($parts[1])) {
                $this->action = $parts[1];
            }
        }
        $index = 2;
        while(count($parts) > $index +1)//  /key/value/key/value
        {
         $cc = ('get_'.$parts[$index]) ;
         $this->registry->template->$cc= $parts[$index+1];
         $index += 2;
        }
        foreach($_GET as $key => $item)///?key=value&key=value
        {
         $cc = ('get_'.key ) ;
         $this->registry->template->$cc= $item;
         $index += 2;
        }
        if (empty($this->controller)) {
            $this->controller = 'index';
        }


        /*** Get action ***/
        if (empty($this->action)) {
            $this->action = 'index';
        }

        /*** set the file path ***/
        $setControllerPath = $this->Controllers[$this->controller];


        if (!empty($setControllerPath)) {
            $Controller_root = __PROTECTED_PATH;
            $Controller_info = explode(':', $setControllerPath);
            if (count($Controller_info) > 1) {
                $this->file = __PROTECTED_PATH . 'plugins/' . $Controller_info[0] . '/controller/' . $Controller_info[1] . 'Controller.php';
            }
            else
            {
                $this->file = $this->path . '/' . $Controller_info[0] . 'Controller.php';
            }
        }
        else
            $this->file = $this->path . '/' . $this->controller . 'Controller.php';
    }


}

?>
