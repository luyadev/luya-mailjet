<?php

namespace luya\mailjet\admin;

/**
 * The mailjet admin Module.
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.3.0
 */
class Module extends \luya\admin\base\Module
{
    /**
     * @var string The api key for the mjml.io parser API.
     * @see https://mjml.io/api
     */
    public $mjmlApiApplicationId;

    /**
     * @var string The api key for the mjml.io parser API.
     * @see https://mjml.io/api
     */
    public $mjmlApiSecretKey;

    public $apis = [
        'api-mailjet-template' => 'luya\mailjet\admin\apis\TemplateController',
        'api-mailjet-templatevariable' => 'luya\mailjet\admin\apis\TemplateVariableController',
    ];
    
    public function getMenu()
    {
        return (new \luya\admin\components\AdminMenuBuilder($this))
            ->node('Mailjet', 'mail')
                ->group('Data')
                    ->itemApi('Templates', 'mailjetadmin/template/index', 'picture_in_picture', 'api-mailjet-template')
                    ->itemApi('Variables', 'mailjetadmin/template-variable/index', 'input', 'api-mailjet-templatevariable');

    }
}