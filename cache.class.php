<?php
/**
 * cache class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2011 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TSqliteCache.php 2996 2011-06-20 15:24:57Z ctrlaltca@gmail.com $
 * @package System.Caching
 */

/**
 * cache class
 *
 *  used from Prado
 * 
 *
 *
 * The following basic cache operations are implemented:
 * - {@link get} : retrieve the value with a key (if any) from cache
 * - {@link set} : store the value with a key into cache
 * - {@link add} : store the value only if cache does not have this key
 * - {@link delete} : delete the value with the specified key from cache
 * - {@link flush} : delete all values from cache
 *
 * Each value is associated with an expiration time. The {@link get} operation
 * ensures that any expired value will not be returned. The expiration time by
 * the number of seconds. A expiration time 0 represents never expire.
 *
 * By definition, cache does not ensure the existence of a value
 * even if it never expires. Cache is not meant to be an persistent storage.
 *
 *
 * Some usage examples of cache are as follows,
 * <code>
 * $cache=new cache;  // cache may also be loaded as a application module
 * $cache->init();
 * $cache->add('object',$object);
 * $object2=$cache->get('object');
 * </code>
 *
 *
 * usually loadded in the application config
 * <code>
 * $CONFIG['modules'] = array(
 *                         "cache" => array('class'=>"TAPCCache"),
 *                         ...
 *                          );
 * </code>
 * later can be called like this
 * <code>
 * $registry->cache->add('apple',data);
 * </code>
 * 
 * there is not reason you can not run multipe caches
 * <code>
 * $CONFIG['modules'] = array(
 *                         "cache" => array('class'=>"TAPCCache"),
 *                         "redis" => array('class'=>"redisCache"),
 *                         ...
 *                          );
 * </code>
 * later can be called like this.
 * <code>
 * $registry->cache->add('apple',data);
 * $registry->redis->add('apple',data);
 * 
 * </code>
 
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: TSqliteCache.php 2996 2011-06-20 15:24:57Z ctrlaltca@gmail.com $
 * @package DCore.cache
 * @since 3.0
 */
 
abstract class cache extends baseClass
    {

function loadCache($cachelist)
{
if (!is_array($cachelist))
   $cachelist = arrray($cachelist);

foreach($cachelist as $key => $item)
  {
   if (is_numeric($key))
     {
     $key = $item;
	 $item = array();
	 }
   if ($key::isAvalible())
   {
      $cache = new $key(); 
	  $cache->init($item);
   }
   }
}  
   

private $_salt = '45_{JKjnsdfgvl8osnvsef';

	protected function generateUniqueKey($key)
	{
		return md5($this->_salt.$key);
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * @param string a key identifying the cached value
	 * @return mixed the value stored in cache, false if the value is not in the cache or expired.
	 */
	public function get($id)
	{
		if(($value=$this->getValue($this->generateUniqueKey($id)))!==false)
		{
			$data=unserialize($value);
			if(!is_array($data))
				return false;
			if(!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged())
				return $data[0];
		}
		return false;
	}

	/**
	 * Stores a value identified by a key into cache.
	 * If the cache already contains such a key, the existing value and
	 * expiration time will be replaced with the new ones. If the value is
	 * empty, the cache key will be deleted.
	 *
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labeled invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function set($id,$value,$expire=0,$dependency=null)
	{
		if(empty($value) && $expire === 0)
			$this->delete($id);
		else
		{
			$data=array($value,$dependency);
			return $this->setValue($this->generateUniqueKey($id),serialize($data),$expire);
		}
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * Nothing will be done if the cache already contains the key or if value is empty.
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labeled invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function add($id,$value,$expire=0,$dependency=null)
	{
		if(empty($value) && $expire === 0)
			return false;
		$data=array($value,$dependency);
		return $this->addValue($this->generateUniqueKey($id),serialize($data),$expire);
	}
	
	
         public function getRef($id,&$value)
         {
		if(($value=$this->getValue($this->generateUniqueKey($id)))!==false)
		{
			$data=unserialize($value);
			if(!is_array($data))
				return false;
                                                                                   
			if(!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged())
			       {
                                $value = $data[0];  
                        	return true;
                               }
		}
		return false;
         }  
	/**
	 * Deletes a value with the specified key from cache
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	public function delete($id)
	{
		return $this->deleteValue($this->generateUniqueKey($id));
	}
abstract protected	 function getValue($key);

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
abstract protected	 function setValue($key,$value,$expire);

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
abstract protected	 function addValue($key,$value,$expire);

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
abstract protected function deleteValue($key);
	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 */
abstract protected	 function flush() ;

    
    }
 /**
  * an sqlite version od cache
  * 
  * 
  * file key in $options is required for module loading
  * 
 * <code>
 * $cache=new sqliteCache;  // cache may also be loaded as a application module
 * $cache->init(array('file'=>sqlitefilepath);
 * $cache->add('object',$object);
 * $object2=$cache->get('object');
 * </code>
  * 
  * <code>
 * $CONFIG['modules'] = array(
 *                         "cache" => array('class'=>"sqliteCache" , 
  *                                         'options'=>array('file'=>sqlitefilepath)
  *                                           ),
 *                         ...
 *                          );
 * </code>

  * @package DCore.cache
  *  
  */
class sqliteCache extends cache
{
	/**
	 * name of the table storing cache data
	 */
	const CACHE_TABLE='cache';
	/**
	 * extension of the db file name
	 */
	const DB_FILE_EXT='.db';

	/**
	 * @var boolean if the module has been initialized
	 */
	private $_initialized=false;
        private $_salt = '45_{JKjnsdfgvl8osnvsef';
	/**
	 * @var SQLiteDatabase the sqlite database instance
	 */
	private $_db=null;
	/**
	 * @var string the database file name
	 */
	private $_file=null;


	/**
	 * Destructor.
	 * Disconnect the db connection.
	 */
	public function __destruct()
	{
		$this->_db=null;
	}

	/**
	 * Initializes this module.
	 * This method is required by the IModule interface. It checks if the DbFile
	 * property is set, and creates a SQLiteDatabase instance for it.
	 * The database or the cache table does not exist, they will be created.
	 * Expired values are also deleted.
	 * @param TXmlElement configuration for this module, can be null
	 * @throws TConfigurationException if sqlite extension is not installed,
	 *         DbFile is set invalid, or any error happens during creating database or cache table.
	 */
	public function init($options)
	{
         $this->_file = $options['file'];
		if(!function_exists('sqlite_open'))
			throw new TConfigurationException('sqlitecache_extension_required');
		if($this->_file===null)
			$this->_file=DCore::getPathOfAlias('runtime') .'/sqlite.cache';
		$error='';
		if(($this->_db=new SQLiteDatabase($this->_file,0666,$error))===false)
			throw new TConfigurationException('sqlitecache_connection_failed',$error);
		if(@$this->_db->query('DELETE FROM '.self::CACHE_TABLE.' WHERE expire<>0 AND expire<'.time())===false)
		{
			if($this->_db->query('CREATE TABLE '.self::CACHE_TABLE.' (key CHAR(128) PRIMARY KEY, value BLOB, expire INT)')===false)
				throw new TConfigurationException('sqlitecache_table_creation_failed',sqlite_error_string(sqlite_last_error()));
		}
		$this->_initialized=true;
	
	}

	/**
	 * @return string database file path (in namespace form)
	 */
	public function getDbFile()
	{
		return $this->_file;
	}

	/**
	 * @param string database file path (in namespace form)
	 * @throws TInvalidOperationException if the module is already initialized
	 * @throws TConfigurationException if the file is not in proper namespace format
	 */
	public function setDbFile($value)
	{
	$this->_file = $value;
        
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key)
	{
		$sql='SELECT value FROM '.self::CACHE_TABLE.' WHERE key=\''.$key.'\' AND (expire=0 OR expire>'.time().') LIMIT 1';
		if(($ret=$this->_db->query($sql))!=false && ($row=$ret->fetch(SQLITE_ASSOC))!==false)
			return $row['value'];
		else
			return false;
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key,$value,$expire)
	{
		$expire=($expire<=0)?0:time()+$expire;
		$sql='REPLACE INTO '.self::CACHE_TABLE.' VALUES(\''.$key.'\',\''.sqlite_escape_string($value).'\','.$expire.')';
		return $this->_db->query($sql)!==false;
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function addValue($key,$value,$expire)
	{
		$expire=($expire<=0)?0:time()+$expire;
		$sql='INSERT INTO '.self::CACHE_TABLE.' VALUES(\''.$key.'\',\''.sqlite_escape_string($value).'\','.$expire.')';
		return @$this->_db->query($sql)!==false;
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key)
	{
		$sql='DELETE FROM '.self::CACHE_TABLE.' WHERE key=\''.$key.'\'';
		return $this->_db->query($sql)!==false;
	}

	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 */
	public function flush()
	{
		return $this->_db->query('DELETE FROM '.self::CACHE_TABLE)!==false;
	}
}

 /**
  * an APC version of cache
  * 
  * will throw an error if APC is not loaded
  * 
  * @package DCore.cache
  *  
  */
class TAPCCache extends cache
{
   /**
    * Initializes this module.
    * This method is required by the IModule interface.
    * @param TXmlElement configuration for this module, can be null
    * @throws TConfigurationException if apc extension is not installed or not started, check your php.ini
    */
	public function init()
	{
	 
        	if(!extension_loaded('apc'))
			throw new TConfigurationException('apccache_extension_required');
				
		if(ini_get('apc.enabled') == false)
			throw new TConfigurationException('apccache_extension_not_enabled');	
			
		if(substr(php_sapi_name(), 0, 3) === 'cli' and ini_get('apc.enable_cli') == false)
			throw new TConfigurationException('apccache_extension_not_enabled_cli');


	}
        
 function __construct($registry,$options = null) {
        parent::__construct($registry,$options);
        $this->scope = $options['org_id'];
 }

       
	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key)
	{
		return apc_fetch($key);
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key,$value,$expire)
	{
		return apc_store($key,$value,$expire);
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function addValue($key,$value,$expire)
	{
		if(function_exists('apc_add')) {
			return apc_add($key,$value,$expire);
		} else {
			throw new TNotSupportedException('apccache_add_unsupported');
		}
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key)
	{
		return apc_delete($key);
	}

	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 */
	public function flush()
	{
		return apc_clear_cache('user');
	}
}
