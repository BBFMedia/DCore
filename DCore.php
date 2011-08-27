<?php
if(!defined('__FRAMEWORK_PATH'))
	define('__FRAMEWORK_PATH',dirname(__FILE__));
if(!defined('DCORE'))
	define('DCORE',dirname(__FILE__));

             
class DCore
{

static function gate($guid,$access = 'R')
{
 return true;
 }
static function authFilter($array , $field,$access = 'R')
{
return $array;
} 
function addSearchPath($path)
{
 global $CONFIG;
 $paths = explode(':',$path);
 if (count($paths) > 1)
 {
         $paths[0] = self::$_aliases[$paths[0]];

     $path = implode('/',$paths);;
 }
 $CONFIG['searchPaths'][] = realpath($path);
}

function getFilePath($filename,$type = '',$view_type='default',$ext = self::CLASS_FILE_EXT)
{
        
        $view_info = explode(':',$filename);
        if (count($view_info) > 1)
         {
         $plugpath = self::$_aliases[$view_info[0]];
         if (empty($plugpath))
         {
        $plugpath = __FRAMEWORK_PATH .'core/'. $view_info[0].'/';
        if (!file_exists($plugpath))
            $plugpath = __PROTECTED_PATH .'plugins/'. $view_info[0].'/';
        }
        if ($type== 'views')
        {
            $file =  $plugpath.'/'.$type.'/'.$view_type.'/'.$view_info[1].$ext;
            if (!file_exists($file))
                 $file =  $plugpath.'/'.$type.'/default/'.$view_info[1].$ext;
            if (!file_exists($file))
                 $file =  $plugpath.'/'.$type.'/'.$view_info[1].$ext;

         if (file_exists($file))
          return $file;

        }
        else
        {
           $file =  $plugpath.'/'.$type.'/'.$view_info[1].$ext;

           if (!file_exists($file))
                $file =  __PROTECTED_PATH .$type.'/'. $view_info[0].$ext;

           if (file_exists($file))
             return $file;
         }
        }
        else
        {
              $file = __PROTECTED_PATH .$type.'/'. $filename.$ext;

           if (file_exists($file))
             return $file;

        }
    	if (file_exists($file) == false)
	{
 
             
		throw new Exception('Path not found in '. $filename);
		return false;
	}

}

  	/**
	 * File extension for Prado class files.
	 */
	const CLASS_FILE_EXT='.php';
    /**
     * @var array list of path aliases
     */
     static $_aliases=array('DCORE'=>DCORE);
    /**
     * @var array list of namespaces currently in use
     */

     static $_usings=array();


	/**
	 * @var array list of class exists checks
	 */
	 static $classExists = array();
  
	public static function getPathOfAlias($alias)
	{
		return isset(self::$_aliases[$alias])?self::$_aliases[$alias]:null;
	}

	protected static function getPathAliases()
	{
		return self::$_aliases;
	}
    /**
	 * Uses a namespace.
	 * A namespace ending with an asterisk '*' refers to a directory, otherwise it represents a PHP file.
	 * If the namespace corresponds to a directory, the directory will be appended
	 * to the include path. If the namespace corresponds to a file, it will be included (include_once).
	 * @param string namespace to be used
	 * @param boolean whether to check the existence of the class after the class file is included
	 * @throws TInvalidDataValueException if the namespace is invalid
	 */
	public static function using($namespace,$type = '',$view_type='default',$checkClassExistence=true)
	{
    		if(isset(self::$_usings[$namespace]) || class_exists($namespace,false))
			return;
  
              if(($path=self::getFilePath($namespace,$type,$view_type))!==null)
		{
			$className=substr($namespace,$pos+1);
			if($className==='*')  // a directory
			{
				self::$_usings[$namespace]=$path;
				set_include_path(get_include_path().PATH_SEPARATOR.$path);
			}
			else  // a file
			{
				self::$_usings[$namespace]=$path;
				if(!$checkClassExistence || !class_exists($className,false))
				{
					try
					{
                  
						include_once($path);

					}
					catch(Exception $e)
					{
				
							throw $e;
					}
				}
			}
		}
		else
			throw new TInvalidDataValueException('prado_using_invalid',$namespace.' - '.$path);
	}
	/**
	 * @param string alias to the path
	 * @param string the path corresponding to the alias
	 * @throws TInvalidOperationException if the alias is already defined
	 * @throws TInvalidDataValueException if the path is not a valid file path
	 */

	public static function setPathOfAlias($alias,$path)
	{
	
	 if(($rp=realpath($path))!==false && is_dir($rp))
		{
			if(strpos($alias,'.')===false)
				self::$_aliases[$alias]=$rp;
			else
				throw new TInvalidDataValueException('prado_aliasname_invalid',$alias);
		}
		else
			throw new TInvalidDataValueException('prado_alias_invalid',$alias,$path);
	

	
	}


    static function redirect($path)
    {
       $host = $_SERVER['HTTP_HOST'];
       $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
       header('Location: http://' . $host . $uri . '/' . $path);
       die();
    }

public static function fatalError($msg)
	{
		echo '<h1>Fatal Error</h1>';
		echo '<p>'.$msg.'</p>';
		if(!function_exists('debug_backtrace'))
			return;
		echo '<h2>Debug Backtrace</h2>';
		echo '<pre>';
		$index=-1;
		foreach(debug_backtrace() as $t)
		{
			$index++;
			if($index==0)  // hide the backtrace of this function
				continue;
			echo '#'.$index.' ';
			if(isset($t['file']))
				echo basename($t['file']) . ':' . $t['line'];
			else
				 echo '<PHP inner-code>';
			echo ' -- ';
			if(isset($t['class']))
				echo $t['class'] . $t['type'];
			echo $t['function'] . '(';
			if(isset($t['args']) && sizeof($t['args']) > 0)
			{
				$count=0;
				foreach($t['args'] as $item)
				{
					if(is_string($item))
					{
						$str=htmlentities(str_replace("\r\n", "", $item), ENT_QUOTES);
						if (strlen($item) > 70)
							echo "'". substr($str, 0, 70) . "...'";
						else
							echo "'" . $str . "'";
					}
					else if (is_int($item) || is_float($item))
						echo $item;
					else if (is_object($item))
						echo get_class($item);
					else if (is_array($item))
						echo 'array(' . count($item) . ')';
					else if (is_bool($item))
						echo $item ? 'true' : 'false';
					else if ($item === null)
						echo 'NULL';
					else if (is_resource($item))
						echo get_resource_type($item);
					$count++;
					if (count($t['args']) > $count)
						echo ', ';
				}
			}
			echo ")\n";
		}
		echo '</pre>';
		exit(1);
	}
}



