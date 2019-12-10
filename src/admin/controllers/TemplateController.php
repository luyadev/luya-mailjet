<?php

namespace luya\mailjet\admin\controllers;

/**
 * Template Controller.
 * 
 * @since 1.3.0
 */
class TemplateController extends \luya\admin\ngrest\base\Controller
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\mailjet\models\Template';
}