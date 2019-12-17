<?php

namespace luya\mailjet\tests\models;

use luya\mailjet\admin\aws\MjmlPreviewActiveWindow;
use luya\mailjet\models\Template;
use luya\mailjet\models\TemplateVariable;
use luya\mailjet\tests\MailjetTestCase;
use luya\testsuite\fixtures\NgRestModelFixture;

class TemplateTest extends MailjetTestCase
{
    public function testRenderOptions()
    {
        $fixture = new NgRestModelFixture([
            'modelClass' => Template::class,
        ]);

        $varFixture = new NgRestModelFixture([
            'modelClass' => TemplateVariable::class,
        ]);

        $model = $fixture->newModel;
        $model->slug = 'foobar';
        $model->mjml = '<content></content>';
        $model->html = '<p>{{%foo}}</p>';

        $this->assertSame('<p>bar</p>', $model->render(['foo' => 'bar']));
        $this->assertSame('<p>{{%foo}}</p>', $model->render());

        $model->html = '<p></p>';
        $this->assertSame('<p></p>', $model->render());

        // test active window context

        $model->off($model::EVENT_BEFORE_INSERT, [$model, 'generateHtmlFromApi']);
        $model->off($model::EVENT_BEFORE_UPDATE, [$model, 'generateHtmlFromApi']);
        $r = $model->save();

        $this->assertSame('<p></p>', $model->renderWithVariables());


        $aw = new MjmlPreviewActiveWindow();
        $aw->ngRestModelClass = get_class($model);
        $aw->setItemId(1);

        $this->assertNotNUll($aw->index());


        $this->expectException('yii\base\InvalidParamException');
        Template::renderHtml('foobar');
    }
}