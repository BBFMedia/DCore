<?php


class DCms extends baseClass{
    private  $_filename = array('variants'=>'variants','mapping'=>'mapping','language'=>'language');
    private $variants;
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
    function setMeta(){
        foreach($this->_activeContent['meta'] as $meta)
             $this->registry->template->addMeta($meta['ref']?$meta['ref']:$meta['name'],$meta);

    }
    function setup($variant_id) {
        global $registry;

        $lang = i18::getLanguage();
        $mapping = $this->mapping[$variant_id];


        $variant    = isset( $this->variants[$mapping]) ? $this->variants[$mapping] : $this->variants[$variant_id];
        $variant    = isset( $variant) ? $variant :  array();

        $langObj    = isset( $this->language[$lang]) ?  $this->language[$lang] : array();
        $default = $this->variants['default'];

        $this->_contentScheme =  $this->_id .'!'.$variant_id.'^'.$lang;

        $variant['content']    = isset( $variant['content']) ?  $variant['content']: array();
        $langObj['content']    = isset( $langObj['content']) ?  $langObj['content']: array();
        $default['content']    = isset( $default['content']) ?  $default['content']: array();

        $variant['meta']    = isset( $variant['meta']) ?  $variant['meta']: array();
        $langObj['meta']    = isset( $langObj['meta']) ?  $langObj['meta']: array();
        $default['meta']    = isset( $default['meta']) ?  $default['meta']: array();

        $content = array_merge($default['content'], $langObj['content']);
        $content = array_merge($content, $variant['content']);

        $meta = array_merge($default['meta'], $langObj['meta']);
        $meta = array_merge($meta, $variant['meta']);

        $result    = array_merge($default, $langObj);
        $result    = array_merge($result, $variant);

        $result['content'] = $content;
        $result['meta'] = $meta;

        $this->_activeContent = $result;

        //set templates
        $this->registry->template->setView('content', $result['view']['content']);
        $this->registry->template->setView('main', $result['view']['main']);

        $this->setMeta();

        return $result;
    }
    function load($namespace){
        $this->_id = $namespace;
        $this->variants      = json_decode(file_get_contents(DCore::getFilePath('root:api/'.$this->_filename['variants'], '', '', '.json')), true);
        $this->mapping = json_decode(file_get_contents(DCore::getFilePath('root:api/'.$this->_filename['mapping'], '', '', '.json')), true);
        $this->language = json_decode(file_get_contents(DCore::getFilePath('root:api/'.$this->_filename['language'], '', '', '.json')), true);


    }
    function text($namespace,$default = null){
        $text = $this->_activeContent['content'][$namespace];
        if (!isset($text))
            $text = isset($default)?$default:$namespace;
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