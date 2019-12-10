<?php

namespace luya\mailjet\tests\models;

use luya\mailjet\models\Template;
use luya\mailjet\tests\MailjetTestCase;
use luya\testsuite\fixtures\NgRestModelFixture;

class TemplateTest extends MailjetTestCase
{
    public function testRenderOptions()
    {
        $fixture = new NgRestModelFixture([
            'modelClass' => Template::class,
        ]);

        $model = $fixture->newModel;
        $model->slug = 'foobar';
        $model->mjml = '<content></content>';
        $model->html = '<p>{{%foo}}</p>';

        $this->assertSame('<p>bar</p>', $model->render(['foo' => 'bar']));
    }
}