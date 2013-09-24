<?php


class DCms extends baseClass{
    private  $_filename = array('varients'=>'varients','mapping'=>'mapping','language'=>'language');
    private $varients;
    private $mapping;
    private $language;
    private $editing = false;
    private $_contentScheme ;

    private $_activeContent;
    function getId(){

        return $this->_id;
    }
    function getContentScheme(){

        return $this->_contentScheme;
    }
    function setFilename($namings){
        $this->_filename = array_merge(   $this->_filename,$namings);
    }
    function setup($varient) {
        global $registry;

        $lang = i18::getLanguage();
        $mapping = $this->mapping[$varient];

        $varient    = isset( $this->varients[$mapping]) ?  $this->varients[$mapping] : array();
        $langObj    = isset( $this->language[$lang]) ?  $this->language[$lang] : array();
        $default = $this->varients['default'];

        $varient['content']    = isset( $varient['content']) ?  $varient['content']: array();
        $langObj['content']    = isset( $langObj['content']) ?  $langObj['content']: array();
        $default['content']    = isset( $default['content']) ?  $default['content']: array();

        $this->_contentScheme =  $this->_id .'!'.$varient.'^'.$lang;
        $content = array_merge($default['content'], $langObj['content']);
        $content = array_merge($content, $varient['content']);

        $result    = array_merge($default, $langObj);
        $result    = array_merge($result, $varient);

        $result['content'] = $content;
        $this->_activeContent = $result;

        //set templates
        $this->registry->template->setView('content', $result['view']['content']);
        $this->registry->template->setView('main', $result['view']['main']);

        return $result;
    }
    function load($namespace){
        $this->_id = $namespace;
        $this->varients      = json_decode(file_get_contents(DCore::getFilePath('root:api/'.$this->_filename['varients'], '', '', '.json')), true);
        $this->mapping = json_decode(file_get_contents(DCore::getFilePath('root:api/'.$this->_filename['mapping'], '', '', '.json')), true);
        $this->language = json_decode(file_get_contents(DCore::getFilePath('root:api/'.$this->_filename['language'], '', '', '.json')), true);


    }
    function text($namespace){
        $text = $this->_activeContent['content'][$namespace];
        $result =  i18::local($text);
        if($this->editing){
            return '<span class="editable-cms" cms-id="'.$this->_contentScheme.'"  cms-id="'.$namespace.'"></span>'.$result;
        }
        return $result;
    }
    function value($namespace){
        return $this->_activeContent[$namespace];
    }
} 