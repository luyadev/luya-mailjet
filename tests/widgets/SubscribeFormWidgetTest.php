<?php

namespace luya\mailjet\tests\widgets;

use luya\mailjet\tests\MailjetTestCase;
use luya\mailjet\widgets\SubscribeFormWidget;
use yii\base\InvalidConfigException;

class SubscribeFormWidgetTest extends MailjetTestCase
{
    public function testRender()
    {
        $w = SubscribeFormWidget::begin(['listId' => 1, 'confirmTemplateId' => 1, 'hashSecret' => 'string']);
        $w::end();

        $this->assertSame(null, $w->modelEmail);
        $this->assertNull($w->isSubscribed);
        $this->assertNull($w->isSent);
    }

    public function testWrongConfiguredException()
    {
        $this->expectException(InvalidConfigException::class);
        SubscribeFormWidget::widget(['listId' => 1, 'hashSecret' => 'foobar']);
    }

    public function testMissingHashAttribute()
    {
        $this->expectException(InvalidConfigException::class);
        SubscribeFormWidget::widget(['listId' => 1]);
    }

    public function testValidConfigWithDoubleOptInt()
    {
        $w = SubscribeFormWidget::begin(['listId' => 1, 'hashSecret' => 'foobar', 'doubleOptIn' => false]);
        $w::end();

        $this->assertNotNull($w->addToList('foo@luya.io'));
        $this->assertNotNull($w->sendConfirmMail('urls'));
        $this->assertNull($w->processConfirmLink());
    }
}
