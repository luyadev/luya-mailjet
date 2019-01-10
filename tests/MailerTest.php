<?php

namespace luya\mailjet\tests;

use luya\mailjet\Mailer;
use luya\mailjet\MailerMessage;


class MailerTest extends MailjetTestCase
{
    public function testDefaultErrorReportingValue()
    {
        $mailer = new Mailer();
        $mailer->defaultTemplateErrorReporting = ['error@luya.io' => 'John Error'];
        $mailer->sandbox = true;

        $message = new MailerMessage();
        $message->setTemplate(123);
        $message->setVariables(['bar' => 'foo']);

        $this->assertSame([
            'TemplateID' => 123,
            'TemplateLanguage' => true,
            'Variables' => [
                'bar' => 'foo',
            ],
            'TemplateErrorReporting' => [
                'Email' => 'error@luya.io',
                'Name' => 'John Error',
            ]

        ], $mailer->extractMessage($message));

        $this->assertFalse($mailer->sendMessage($message));
        $this->assertNotEmpty($mailer->lastError);
    }
}