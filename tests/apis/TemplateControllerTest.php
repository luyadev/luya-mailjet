<?php

namespace luya\mailjet\tests\apis;

use luya\mailjet\admin\apis\TemplateController;
use luya\mailjet\admin\controllers\TemplateController as ControllersTemplateController;
use luya\mailjet\models\Template;
use luya\testsuite\cases\NgRestTestCase;

class TemplateControllerTeste extends NgRestTestCase
{
    public $modelClass = Template::class;

    public $apiClass = TemplateController::class;

    public $controllerClass = ControllersTemplateController::class;

    public function getConfigArray()
    {
        return [
            'id' => 'id',
            'basePath' => dirname(__DIR__),
        ];
    }
}