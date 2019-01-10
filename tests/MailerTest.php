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
        $message->setVariables([
            'bar' => 'foo',
            'int' => 123,
            'null' => null,
            'bool' => false,    
        ]);

        $this->assertSame([
            'TemplateID' => 123,
            'TemplateLanguage' => true,
            'Variables' => [
                'bar' => 'foo',
                'int' => '123',
                'null' => '',
                'bool' => '',
            ],
            'TemplateErrorReporting' => [
                'Email' => 'error@luya.io',
                'Name' => 'John Error',
            ],
            'TemplateErrorDeliver' => true,
        ], $mailer->extractMessage($message));

        $this->assertFalse($mailer->sendMessage($message));
        $this->assertNotEmpty($mailer->lastError);
    }
}