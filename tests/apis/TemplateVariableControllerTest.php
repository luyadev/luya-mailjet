<?php

namespace luya\mailjet\tests\apis;

use luya\mailjet\admin\apis\TemplateVariableController;
use luya\mailjet\admin\controllers\TemplateVariableController as ControllersTemplateVariableController;
use luya\mailjet\models\TemplateVariable;
use luya\testsuite\cases\NgRestTestCase;

class TemplateVariableControllerTest extends NgRestTestCase
{
    public $modelClass = TemplateVariable::class;

    public $apiClass = TemplateVariableController::class;

    public $controllerClass = ControllersTemplateVariableController::class;

    public function getConfigArray()
    {
        return [
            'id' => 'id',
            'basePath' => dirname(__DIR__),
        ];
    }
}