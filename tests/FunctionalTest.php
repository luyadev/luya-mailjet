<?php

namespace luya\mailjet\tests;

use luya\mailjet\MailerMessage;

class FunctionalTest extends MailjetTestCase
{
    public function testNotEmptyTemplateVariables()
    {
        $mailer = new MailerMessage();
        $mailer->setVariables(['foo' => false, 'bar' => null, 'baz' => 'foo']);

        $this->assertSame(['foo' => '', 'bar' => '', 'baz' => 'foo'], $mailer->getVariables());
    }
}
