<?php

namespace luya\mailjet;

/**
 * Convert MJML to json/array.
 * 
 * This is built from the https://app.mailjet.com/passport/api-fetchr POST request.
 * 
 * Tested and made for passport version 3.3.5
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
Class Mjml
{
    public $charMapping = [
        '&nbsp;' => ' ',
        '<br>' => '<br />',
    ];
    
    public static $errors;
    
    public static function getArray($mjml)
    {
        $object = new self();
        return $object->load($mjml);
    }
    
    public static function getJson($mjml)
    {
        $array = self::getArray($mjml);
        
        if (!$array) {
            return false;
        }
        
        return json_encode($array);
    }
    
    protected function load($mjml)
    {
        // prevent casual xml errors
        $mjml = str_replace(array_keys($this->charMapping), array_values($this->charMapping), trim($mjml));
        
        if (!$this->validateXml($mjml)) {
            return false;
        }
        $a = $this->mjmlToArray(trim($mjml));
        $array = $this->generateStructure($a);
        
        return $array;
    }
    
    protected function validateXml($xmlContent, $version = '1.0', $encoding = 'utf-8')
    {
        if (trim($xmlContent) == '') {
            return false;
        }
        
        libxml_use_internal_errors(true);
        
        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($xmlContent);
        
        $errors = libxml_get_errors();
        libxml_clear_errors();
        
        static::$errors = $errors;
        
        return empty($errors);
    }
    
    protected function generateStructure(XmlElement $elmn)
    {
        $attributes = $elmn->attributes;
        
        // auto inject passport version as its maybe required:
        if (isset($attributes['passport'])) {
            $attributes['passport'] = ['version' => $attributes['passport']];
        }
        
        $item = [
            'tagName' => $elmn->name,
            'children' => [],
            'attributes' => $attributes,
        ];
        
        if ($elmn->content !== null) {
            $item['content'] = $elmn->content;   
        }
        
        foreach ($elmn->children as $child) {
            $item['children'][] = $this->generateStructure($child);
        }
        
        return $item;
    }
        
    protected function mjmlToArray($mjml)
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $mjml, $tags);
        xml_parser_free($parser);
        
        $elements = []; // the currently filling [child] XmlElement array
        $stack = [];
        foreach ($tags as $tag) {
            $index = count($elements);
            if ($tag['type'] == "complete" || $tag['type'] == "open") {
                $elements[$index] = new XmlElement;
                $elements[$index]->name = $tag['tag'];
                $elements[$index]->attributes = isset($tag['attributes']) ? $tag['attributes'] : [];
                $elements[$index]->content = isset($tag['value']) ? $tag['value'] : null;
                if ($tag['type'] == "open") {  // push
                    $elements[$index]->children = [];
                    $stack[count($stack)] = &$elements;
                    $elements = &$elements[$index]->children;
                }
            }
            if ($tag['type'] == "close") {  // pop
                $elements = &$stack[count($stack) - 1];
                unset($stack[count($stack) - 1]);
            }
        }
        
        return $elements[0];
    }
}

class XmlElement {
    var $name;
    var $attributes = [];
    var $content;
    var $children = [];
};