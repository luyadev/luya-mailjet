<?php

namespace luya\mailjet\admin\apis;

/**
 * Template Variable Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class TemplateVariableController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\mailjet\models\TemplateVariable';
}