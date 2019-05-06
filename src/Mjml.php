<?php

namespace luya\mailjet;

/**
 * Convert MJML to json/array.
 *
 * The main goal of this helper method is to generate valid json code from mjml string
 * in order to work with miljate passport html editor.
 *
 * > This is built from the https://app.mailjet.com/passport/api-fetchr POST request.
 * > Tested and made for passport version 3.3.5
 *
 * Since version 1.1.0 tha tags in $rawElements list will be compiled with its RAW content witout
 * allowance of nesting of child elements. For example <mj-text><a href="luya.io"></a></mj-text> will
 * wrap the `<a href="luya.io"></a>` into CDATA even for newlines!
 *
 * ```php
 * $passportJson = Mjml::getJson('<mj-section>
 *     <mj-column>
 *         <mj-text>Read more!</mj-text>
 *         <mj-text><a href="https://luya.io">luya.io</a></mj-text>
 *     </mj-column>
 * </mj-section>');
 * ```
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Mjml
{
    /**
     * @var array A list of chars which will be transcoded (search left, replace with value)
     */
    public $charMapping = [
        '&nbsp;' => ' ',
        '<br>' => '<br />',
    ];

    /**
     * @var array An array with elements where the content between those tags will be used as raw content (which means wrap with CDATA tag).
     * @since 1.1.0
     */
    public $rawElements = [
        'mj-text',
    ];
    
    /**
     * @var array An array which holds all xml parser errors.
     */
    public static $errors = [];
    
    /**
     * Get the mjml array from a string
     *
     * @param string $mjml
     * @return array
     */
    public static function getArray($mjml)
    {
        $object = new self();
        return $object->load($mjml);
    }
    
    /**
     * Get the json string from the mjml content string.
     *
     * This generates the mailjet passport valid content
     *
     * @param string $mjml
     * @return string A json valid for mailjet passport editor.
     */
    public static function getJson($mjml)
    {
        $array = self::getArray($mjml);
        
        if (!$array) {
            return false;
        }
        
        return json_encode($array);
    }

    /**
     * In order to prevent child attribute generation for html valid raw elements like <mj-text> try
     * to wrap the content in CDATA tags.
     *
     * @param string $content
     * @return string
     * @since 1.1.0
     */
    protected function wrapCdataForRawElements($content)
    {
        foreach ($this->rawElements as $name) {
            preg_match_all('/<'.preg_quote($name, '/').'>(.*)<\/'.preg_quote($name, '/').'>/s', $content, $result, PREG_SET_ORDER);

            foreach ($result as $match) {
                $content = str_replace($match[1], '<![CDATA['.$match[1].']]>', $content);
            }
        }

        return $content;
    }
    
    /**
     * Get the parsed and nested php array from the mjml string
     *
     * @param string $mjml
     * @return array
     */
    protected function load($mjml)
    {
        // prevent casual xml errors
        $mjml = str_replace(array_keys($this->charMapping), array_values($this->charMapping), trim($mjml));
        
        // validate whether the xml input is valid or not.
        if (!$this->validateXml($mjml)) {
            return false;
        }

        // generate the array of elements
        $array = $this->mjmlToArray($this->wrapCdataForRawElements($mjml));
        // starte structure parsing
        return $this->generateStructure($array);
    }
    
    /**
     * Validate the current xml content (which is the mjml input).
     *
     * @param string $xmlContent
     * @param string $version
     * @param string $encoding
     * @return boolean
     */
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
    
    /**
     * Generate the structure
     *
     * @param MjmlXmlElement $elmn
     * @return array
     */
    protected function generateStructure(MjmlXmlElement $elmn)
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
        
    /**
     * Load the mjml fcontent and return as array.
     *
     * @param [type] $mjml
     * @return MjmlXmlElement
     */
    protected function mjmlToArray($mjml)
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $mjml, $tags);
        xml_parser_free($parser);
        
        $elements = []; // the currently filling [child] MjmlXmlElement array
        $stack = [];
        foreach ($tags as $tag) {
            $index = count($elements);
            if ($tag['type'] == "complete" || $tag['type'] == "open") {
                $elements[$index] = new MjmlXmlElement;
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