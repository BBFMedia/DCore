<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of i18
 *
 * @author adrian
 */
if (!defined('_DEFAULT_LANGUAGE_'))
    define('_DEFAULT_LANGUAGE_','eng');

class i18 {
    
    static $language_id = _DEFAULT_LANGUAGE_;
    
    function setLanguage($lang)
    {
        if (!empty($lang))
          self::$language_id = $lang;
    }
    function getLanguage()
    {
    return self::$language_id ;
    }
    function loadLanguageFile($filename = '')
    {
        if (empty($filename))
            $filename = self::getLanguage();
        $filename = 'protected:settings/lang/'.self::getLanguage().'/'.$filename;
if (DCore::getFilePath($filename,'','','.php',false))
  DCore::using($filename);
    }
}

?>
