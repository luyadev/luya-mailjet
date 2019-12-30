<?php

namespace luya\mailjet;

/**
 * The XML element Node.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.1.0
 */
class MjmlXmlElement
{
    /**
     * @var string The name of the node
     */
    public $name;

    /**
     * @var array A list of attributes.
     */
    public $attributes = [];

    /**
     * @var string The content of the node.
     */
    public $content;

    /**
     * @var array A list of children nodes.
     */
    public $children = [];
};
