<?php


class DCms extends baseClass {
    private $_filename = array('variants' => 'variants', 'mapping' => 'mapping', 'language' => 'language');
    private $variants;
    private $mapping;
    private $language;
    private $default;
    private $editing = false;

    private $_layers;
    private $_contentScheme;

    private $_activeContent;

    function getId() {

        return $this->_id;
    }

    function getContentScheme() {

        return $this->_contentScheme;
    }

    function setFilename($namings) {
        $this->_filename = array_merge($this->_filename, $namings);
    }

    function setMeta() {
        foreach ($this->_activeContent['meta'] as $meta)
            $this->registry->template->addMeta($meta['ref'] ? $meta['ref'] : $meta['name'], $meta);

    }

    private function mergeLayers($layers) {

        $result            = array();
        $result['content'] = array();
        $result['meta']    = array();
        $content           = array();
        $meta              = array();

        foreach ($layers as $layer) {
            if (isset($layer['content']))
                $content = array_merge($content, $layer['content']);
            if (isset($layer['meta']))
                $meta = array_merge($meta, $layer['meta']);

            $result = array_merge($result, $layer);
        }
        $result['content'] = $content;
        $result['meta']    = $meta;

        return $result;
    }

    function setup($variant_id) {

        $this->_layers = array();
        $mapping       = $this->mapping[$variant_id];

        $variant = isset($this->variants[$mapping]) ? $this->variants[$mapping] : $this->variants[$variant_id];
        $variant = isset($variant) ? $variant : null;
        if ($variant) {
            $this->_layers[] = $variant;
            if (isset($variant['lang'])) {
                i18::setLanguage($variant['lang']);
            }
        }
        $lang = i18::getLanguage();

        $langObj = isset($this->language[$lang]) ? $this->language[$lang] : null;
        if ($langObj)
        $this->_layers[] = $langObj;
        $this->_layers[] = $this->default;

        $this->_contentScheme = $this->_id . '!' . $variant_id . '^' . $lang;
        $result = $this->mergeLayers($this->_layers);
        $this->_activeContent = $result;

        //set templates
        $this->registry->template->setView('content', $result['view']['content']);
        $this->registry->template->setView('main', $result['view']['main']);

        $this->setMeta();

        return $result;
    }

    function load($namespace) {
        $this->_id      = $namespace;

        // todo:
        // get from mongo
        // if version is different than remote then on file then load.
        // this could be slow because it has to check everytime it loads.
        // though I do think this should all be cached

        $data           = json_decode(file_get_contents(DCore::getFilePath($namespace, 'data', '', '.json')), true);
        $this->variants = $data['variants'];
        $this->mapping  = $data['mapping'];
        $this->language = $data['language'];
        $this->default  = $data['default'];

    }

    function text($namespace, $default = null) {
        $text = $this->_activeContent['content'][$namespace];
        if (!isset($text))
            $text = isset($default) ? $default : $namespace;
        $result = i18::local($text);
        if ($this->editing) {
            return '<span class="editable-cms" cms-id="' . $this->_contentScheme . '"  cms-id="' . $namespace . '"></span>' . $result;
        }

        return $result;
    }

    function value($namespace) {
        return $this->_activeContent[$namespace];
    }
} 