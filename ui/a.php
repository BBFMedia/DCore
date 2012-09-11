<?php


/**
 * :ui:a
 *
 * adds controller/action relization.
 * 
 * two ways to use.
 * 
 * prefered is
 * <code>
 *   <ui:a controller="controlname" action="actionname" params={array('param1'=>'data')} > </a>
 *</code> 
 * 
 * @author adrian
 * @package DCore/UI
 */

class :ui:a extends :ui:element {
  attribute
     string controller , string action,
     array params,

    string href, string name, string rel, string target;
  category %flow, %phrase, %interactive;
   
  // transparent
  // may not contain %interactive
  children (pcdata | %flow)*;
  protected $tagName = 'a';
     function stringify()
   {
    //  return <span/>;
       $controller = $this->getAttribute('controller');
       $href = $this->getAttribute('href');
       if ($controller)
       {
       $action = $this->getAttribute('action');
       $params = $this->getAttribute('params');    
       $url = $this->registry->router->buildURL($controller,$action,$params);
        $this->setAttribute('href',$url);
       } 
       else if (preg_match("/^dcore[:]/i",$href)) 
       {
           $params = explode('/',substr($href,6,10000000));
           $controller = $params[0];
           $action = $params[1];
         
           $index = 2;
            while (count($params) > $index + 1) {
                $newparams[$params[$index]] = $params[$index+1];
               $index += 2;
            }
           $url = $this->registry->router->buildURL($controller,$action,$newparams);
         $this->setAttribute('href',$url);
  
       }
           
       return parent::stringify();
   }
}