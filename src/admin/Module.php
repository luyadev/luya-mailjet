<?php

namespace luya\mailjet\admin;

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
            ->node('Template', 'extension')
                ->group('Group')
                    ->itemApi('Template', 'mailjetadmin/template/index', 'label', 'api-mailjet-template')
                    ->itemApi('TemplateVariable', 'mailjetadmin/template-variable/index', 'label', 'api-mailjet-templatevariable');

    }
}