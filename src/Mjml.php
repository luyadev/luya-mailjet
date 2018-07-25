<?php

namespace luya\mailjet;

/**
 * Convert MJML to json/array.
 * 
 * This is built from the https://app.mailjet.com/passport/api-fetchr POST request.
 * 
 * Tested and made for passport version 3.3.5
 * 
 * A full example payload extracted from api fetchr:
 * 
 * ```json
{
  "MJMLContent": {
    "tagName": "mj-section",
    "children": [
      {
        "tagName": "mj-column",
        "attributes": {},
        "children": [
          {
            "tagName": "mj-image",
            "attributes": {
              "src": "http://191n.mj.am/tplimg/191n/b/040q/qz8m.png",
              "align": "center",
              "width": "250px",
              "height": "auto",
              "padding-bottom": "0px",
              "alt": "",
              "href": "",
              "border": "none",
              "padding": "10px 25px",
              "target": "_blank",
              "border-radius": "",
              "title": "",
              "padding-top": "20px"
            }
          }
        ]
      },
      {
        "tagName": "mj-column",
        "attributes": {},
        "children": [
          {
            "tagName": "mj-text",
            "content": "<p style=\"margin: 10px 0;\">The content of your email goes here.</p><p style=\"margin: 10px 0;\">You can drag and drop blocks of text, images, buttons or other content elements to add them to your message. Customize the font and the colors. Add links to track clicks.</p>",
            "attributes": {
              "align": "left",
              "color": "#55575d",
              "font-family": "Arial, sans-serif",
              "font-size": "13px",
              "line-height": "22px",
              "padding": "10px 25px",
              "padding-bottom": "0px",
              "padding-top": "20px"
            }
          }
        ]
      }
    ],
    "attributes": {
      "background-repeat": "repeat",
      "padding": "0px 0px 0px 0px",
      "background-size": "auto",
      "background-color": "#ffffff",
      "text-align": "center",
      "vertical-align": "top",
      "passport": {
        "version": "3.3.5"
      }
    }
  }
}
 * ```
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