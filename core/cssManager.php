<?php


/**
 * Description of cssManager
 *  
 * 1) cache css descriptions. 
 *    $description[id] = array('asset' = >$namespace,'src'=>$css)
 * 
 * 
 * 2) session what css is used
 *    $files[] = array('cssid','cssid');
 * 
 * 
 * 
 * @author adrian
 */
require __SASS_PATH . '/SassParser.php';


class cssManager {

  static $assetPath = '';
  static $_cache = array();
  static $_session = array();
  function getCache()
  {
    return   self::$_cache ;
  }
 function setCache($cache)
  {
    self::$_cache = $cache;
  }
  function getSession()
  {
    return   self::$_session ;
  }
 function setSession($session)
  {
    self::$_session = $session;
  }
  
  static function addDescription($id,$nameSpace,$src = false)
  {
    self::$_cache[$id] = array('assets'=>$nameSpace,'src'=>$src); 
  }
  /**
   *adds a css file to a css list
   * @param type $csNameSpace 
   */
  static function addCSS($cssNameSpace)
  {
    $id =  self::udihash($cssNameSpace);
    self::addDescription($id,$cssNameSpace);
  }
  /** addes css as a string
   * 
   * if asset is a name space the it will publish the assets. if it is a url then it 
   * uses it a a url. 
   *
   * @param string $cssID is a unique id for the css
   * @param string $css
   * @param string $assets  the name space of the css file 
   */
  static function addCSSString($cssID,$css,$assets = false)
  {
    $id = self::udihash($cssID);
   self::addDescription($id,$assets,$css);
   
  }
  
  
  
  static function _convertUrl($url)
  {
   if (preg_match("/^\//",$url)) {
     return $url;
    }
   else if (preg_match("/:\/\//",$url)) {
     return $url;
    }
    else
    {
      return self::$assetPath .'/'.$url ;
    }
  
  }
  static function url($url) {
    
    $url = self::_convertUrl($url);
    $url = 'url(' . $url . ')';

    return new SassString($url);
  }
  function render($filename,$isFile = true) {


    $sass = new SassParser(array('syntax' => SassFile::SCSS, 'vendor_properties' => array('theme' => 'white'),
       'functions' => array('url' => 'cssManager::url')
      ));

    $result = $sass->toCss($filename, $isFile);

    return $result;
  }
  
  private static $golden_primes = array(
		1,41,2377,147299,9132313,566201239,35104476161,2176477521929
	);
 
	/* Ascii :                    0  9,         A  Z,         a  z     */
	/* $chars = array_merge(range(48,57), range(65,90), range(97,122)) */
	private static $chars = array(
		0=>48,1=>49,2=>50,3=>51,4=>52,5=>53,6=>54,7=>55,8=>56,9=>57,10=>65,
		11=>66,12=>67,13=>68,14=>69,15=>70,16=>71,17=>72,18=>73,19=>74,20=>75,
		21=>76,22=>77,23=>78,24=>79,25=>80,26=>81,27=>82,28=>83,29=>84,30=>85,
		31=>86,32=>87,33=>88,34=>89,35=>90,36=>97,37=>98,38=>99,39=>100,40=>101,
		41=>102,42=>103,43=>104,44=>105,45=>106,46=>107,47=>108,48=>109,49=>110,
		50=>111,51=>112,52=>113,53=>114,54=>115,55=>116,56=>117,57=>118,58=>119,
		59=>120,60=>121,61=>122
	);
 

 
	public static function udihash($str, $len = 5) {
		$ceil = pow(62, $len);
    $num = crc32 ($str);
		$prime = self::$golden_primes[$len];
		$dec = ($num * $prime)-floor($num * $prime/$ceil)*$ceil;
	  $key = "";
		while($dec > 0) {
			$mod = $dec-(floor($dec/62)*62);
			$key .= chr(self::$chars[$mod]);
			$dec = floor($dec/62);
		}
		$hash = strrev($key);
		return str_pad($hash, $len, "0", STR_PAD_LEFT);
	}

}

