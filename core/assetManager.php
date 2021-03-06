<?php

/**
 * TAssetManager class
 *

  usesd from Prado
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2011 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TAssetManager.php 2996 2011-06-20 15:24:57Z ctrlaltca@gmail.com $
 * @package DCore/core
 */

/**
 * TAssetManager class
 *
 * TAssetManager provides a scheme to allow web clients visiting
 * private files that are normally web-inaccessible.
 *
 * TAssetManager will copy the file to be published into a web-accessible
 * directory. The default base directory for storing the file is "assets", which
 * should be under the application directory. This can be changed by setting
 * the {@link setBasePath BasePath} property together with the
 * {@link setBaseUrl BaseUrl} property that refers to the URL for accessing the base path.
 *
 * By default, TAssetManager will not publish a file or directory if it already
 * exists in the publishing directory and has an older modification time.
 * If the application mode is set as 'Performance', the modification time check
 * will be skipped. You can explicitly require a modification time check
 * with the function {@link publishFilePath}. This is usually
 * very useful during development.
 *
 * TAssetManager may be configured in application configuration file as follows,
 * <code>
 * <module id="asset" BasePath="Application.assets" BaseUrl="/assets" />
 * </code>
 * where {@link getBasePath BasePath} and {@link getBaseUrl BaseUrl} are
 * configurable properties of TAssetManager. Make sure that BasePath is a namespace
 * pointing to a valid directory writable by the Web server process.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: TAssetManager.php 2996 2011-06-20 15:24:57Z ctrlaltca@gmail.com $
 * @package DCore
 * @since 3.0
 */
class assetManager extends baseClass {
    /**
     * Default web accessible base path for storing private files
     */

    const DEFAULT_BASEPATH = 'assets';

    /**
     * @var string base web accessible path for storing private files
     */
    private $_basePath = null;

    /**
     * @var string base URL for accessing the publishing directory.
     */
    private $_baseUrl = null;

    /**
     * @var boolean whether to use timestamp checking to ensure files are published with up-to-date versions.
     */
    private $_checkTimestamp = true;

    /**
     * @var TApplication application instance
     */
    private $_application;

    /**
     * @var array published assets
     */
    private $_published = array();

    /**
     * @var boolean whether the module is initialized
     */
    private $_initialized = false;

    /**
     * Initializes the module.
     * This method is required by IModule and is invoked by application.
     * @param TXmlElement module configuration
     */
    public function init($options = null, $url = null) {


    }

    function __construct($registry, $options = null) {
        $this->_basePath = __ROOT_PATH . self::DEFAULT_BASEPATH;
        $this->_baseUrl = URL_ROOT . self::DEFAULT_BASEPATH;

        if ($options['basepath'])
            $this->_basePath = $options['basepath'];
        if ($options['url'])
            $this->_baseUrl = $options['url'];
        if ($options['checktimestamp'])
            $this->_checkTimestamp = !empty($options['checktimestamp']);
        parent::__construct($registry);
        if (!is_writable($this->_basePath) || !is_dir($this->_basePath))
            throw new Exception('Asset Foloder must exists at ' . $this->_basePath);
    }

    /**
     * @return string the root directory storing published asset files
     */
    public function getBasePath() {
        return $this->_basePath;
    }

    /**
     * Sets the root directory storing published asset files.
     * The directory must be in namespace format.
     * @param string the root directory storing published asset files
     * @throws TInvalidOperationException if the module is initialized already
     */
    public function setBasePath($value) {

        $this->_basePath = $value;
    }

    /**
     * @return string the base url that the published asset files can be accessed
     */
    public function getBaseUrl() {
        return $this->_baseUrl;
    }

    /**
     * @param string the base url that the published asset files can be accessed
     * @throws TInvalidOperationException if the module is initialized already
     */
    public function setBaseUrl($value) {

        $this->_baseUrl = rtrim($value, '/');
    }

    public function getResource($id) {
        $res_id = md5($id);
        return $this->_resources['res' . $res_id];
    }

    public function publishResource($id, $resource) {
        $res_id = md5($id);
        if (isset($this->_resources[$resource . $res_id]))
            return $this->_resources[$resource . $res_id];
        $dir = 'res';
        $dst = $this->_basePath . DIRECTORY_SEPARATOR . $dir;
        if (!file_exists($dst . DIRECTORY_SEPARATOR . $res_id))
            file_put_contents($dst . DIRECTORY_SEPARATOR . $res_id, $resource);
        $this->_published[$dir . $res_id]['url'] = $dst . DIRECTORY_SEPARATOR . $res_id;
        $this->_published[$dir . $res_id]['url'] = $this->_baseUrl . '/' . $dir . '/' . $fileName;
        return $this->_published[$dir . $res_id];
    }

    /**
     * Publishes a file or a directory (recursively).
     * This method will copy the content in a directory (recursively) to
     * a web accessible directory and returns the URL for the directory.
     * If the application is not in performance mode, the file modification
     * time will be used to make sure the published file is latest or not.
     * If not, a file copy will be performed.
     * @param string the path to be published
     * @param boolean If true, file modification time will be checked even if the application
     * is in performance mode.
     * @return string an absolute URL to the published directory
     * @throws TInvalidDataValueException if the file path to be published is
     * invalid
     */
    public function publishFilePath($path, $ext = '') {
        //	$path = str_replace('//' ,'/', $path);

        if (isset($this->_published[$path . $ext]))
            return $this->_published[$path . $ext]['url'];


        $fullpath = DCore::getFilePath($path, '', '', $ext);
        $fullpath = str_replace('\\', DIRECTORY_SEPARATOR, $fullpath);
        $fullpath = str_replace('/', DIRECTORY_SEPARATOR, $fullpath);
        $fullpath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $fullpath);

        if (empty($path) || ($fullpath === false))
            die('assetmanager_filepath_invalid' . $path);
        else if (is_file($fullpath)) {
            $dir = dirname($fullpath);

            $dir = $this->hash(dirname($fullpath));
            $fileName = basename($fullpath);
            $dst = $this->_basePath . DIRECTORY_SEPARATOR . $dir;
            if (!is_file($dst . DIRECTORY_SEPARATOR . $fileName) or $this->_checkTimestamp)
                $this->copyFile($fullpath, $dst);
            return $this->_published[$path . $ext]['url'] = $this->_baseUrl . '/' . $dir . '/' . $fileName;
        }
        else {
            $dir = dirname($fullpath . "/test");

            $dir = $this->hash($dir);

            if (!is_dir($this->_basePath . DIRECTORY_SEPARATOR . $dir) or $this->_checkTimestamp) {
                $this->copyDirectory($fullpath, $this->_basePath . DIRECTORY_SEPARATOR . $dir);
            }
            return $this->_published[$path . $ext]['url'] = $this->_baseUrl . '/' . $dir;
        }
    }

    /**
     * @return array List of published assets
     * @since 3.1.6
     */
    protected function getPublished() {
        return $this->_published;
    }

    /**
     * @param $values List of published assets
     * @since 3.1.6
     */
    protected function setPublished($values = array()) {
        $this->_published = $values;
    }

    /**
     * Returns the published path of a file path.
     * This method does not perform any publishing. It merely tells you
     * if the file path is published, where it will go.
     * @param string directory or file path being published
     * @return string the published file path
     */
    public function getPublishedPath($path) {
        $path = realpath($path);
        if (is_file($path))
            return $this->_basePath . DIRECTORY_SEPARATOR . $this->hash(dirname($path)) . DIRECTORY_SEPARATOR . basename($path);
        else
            return $this->_basePath . DIRECTORY_SEPARATOR . $this->hash($path);
    }

    /**
     * Returns the URL of a published file path.
     * This method does not perform any publishing. It merely tells you
     * if the file path is published, what the URL will be to access it.
     * @param string directory or file path being published
     * @return string the published URL for the file path
     */
    public function getPublishedUrl($path) {
        $path = realpath($path);
        if (is_file($path))
            return $this->_baseUrl . '/' . $this->hash(dirname($path)) . '/' . basename($path);
        else
            return $this->_baseUrl . '/' . $this->hash($path);
    }

    /**
     * Generate a CRC32 hash for the directory path. Collisions are higher
     * than MD5 but generates a much smaller hash string.
     * @param string string to be hashed.
     * @return string hashed string.
     */
    protected function hash($dir) {
        return sprintf('%x', crc32($dir . '45d6'));
    }

    /**
     * Copies a file to a directory.
     * Copying is done only when the destination file does not exist
     * or has an older file modification time.
     * @param string source file path
     * @param string destination directory (if not exists, it will be created)
     */
    protected function copyFile($src, $dst) {
        //      echo        $src;
        if (!is_dir($dst)) {
            @mkdir($dst);
            @chmod($dst, 0777);
        }
        $dstFile = $dst . DIRECTORY_SEPARATOR . basename($src);
        //  echo ($dstFile);

        if (@filemtime($dstFile) < @filemtime($src)) {

            @copy($src, $dstFile);
        }
    }

    /**
     * Copies a directory recursively as another.
     * If the destination directory does not exist, it will be created.
     * File modification time is used to ensure the copied files are latest.
     * @param string the source directory
     * @param string the destination directory
     * @todo a generic solution to ignore certain directories and files
     */
    public function copyDirectory($src, $dst) {
        if (!is_dir($dst)) {
            @mkdir($dst);
            @chmod($dst, PRADO_CHMOD);
        }
        if ($folder = @opendir($src)) {
            while ($file = @readdir($folder)) {
                if ($file === '.' || $file === '..' || $file === '.svn')
                    continue;
                else if (is_file($src . DIRECTORY_SEPARATOR . $file)) {
                    if (@filemtime($dst . DIRECTORY_SEPARATOR . $file) < @filemtime($src . DIRECTORY_SEPARATOR . $file)) {
                        @copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                        @chmod($dst . DIRECTORY_SEPARATOR . $file, PRADO_CHMOD);
                    }
                }
                else
                    $this->copyDirectory($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
            }
            closedir($folder);
        } else {
            throw new TInvalidDataValueException('assetmanager_source_directory_invalid', $src);
        }
    }

    /**
     * Publish a tar file by extracting its contents to the assets directory.
     * Each tar file must be accomplished with its own MD5 check sum file.
     * The MD5 file is published when the tar contents are successfully
     * extracted to the assets directory. The presence of the MD5 file
     * as published asset assumes that the tar file has already been extracted.
     * @param string tar filename
     * @param string MD5 checksum for the corresponding tar file.
     * @param boolean Wether or not to check the time stamp of the file for publishing. Defaults to false.
     * @return string URL path to the directory where the tar file was extracted.
     */
    public function publishTarFile($tarfile, $md5sum, $checkTimestamp = false) {
        if (isset($this->_published[$md5sum]))
            return $this->_published[$md5sum]['url'];
        else if (($fullpath = realpath($md5sum)) === false || !is_file($fullpath))
            throw new TInvalidDataValueException('assetmanager_tarchecksum_invalid', $md5sum);
        else {
            $dir = $this->hash(dirname($fullpath));
            $fileName = basename($fullpath);
            $dst = $this->_basePath . DIRECTORY_SEPARATOR . $dir;
            if (!is_file($dst . DIRECTORY_SEPARATOR . $fileName) || $checkTimestamp || $this->getApplication()->getMode() !== TApplicationMode::Performance) {
                if (@filemtime($dst . DIRECTORY_SEPARATOR . $fileName) < @filemtime($fullpath)) {
                    $this->copyFile($fullpath, $dst);
                    $this->deployTarFile($tarfile, $dst);
                }
            }
            return $this->_published[$md5sum]['url'] = $this->_baseUrl . '/' . $dir;
        }
    }

    /**
     * Extracts the tar file to the destination directory.
     * N.B Tar file must not be compressed.
     * @param string tar file
     * @param string path where the contents of tar file are to be extracted
     * @return boolean true if extract successful, false otherwise.
     */
    protected function deployTarFile($path, $destination) {
        if (($fullpath = realpath($path)) === false || !is_file($fullpath))
            throw new TIOException('assetmanager_tarfile_invalid', $path);
        else {
            Prado::using('System.IO.TTarFileExtractor');
            $tar = new TTarFileExtractor($fullpath);
            return $tar->extract($destination);
        }
    }

}

