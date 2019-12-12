<?php

namespace luya\mailjet\tests\models;

use luya\mailjet\models\TemplateVariable;
use luya\mailjet\tests\MailjetTestCase;
use luya\testsuite\fixtures\NgRestModelFixture;

class TemplateVariableTest extends MailjetTestCase
{
    public function testRenderOptions()
    {
        $fixture = new NgRestModelFixture([
            'modelClass' => TemplateVariable::class,
        ]);
    }
}