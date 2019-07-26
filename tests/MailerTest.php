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
        $message->setDeduplicateCampaign(false);
        $message->setCustomId('MyCustomId');
        $message->setCustomCampaign('MyCustomCampaign');
        $message->setTemplateLanguage(false);

        $this->assertSame([
            'TemplateID' => 123,
            'TemplateLanguage' => false,
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
            'CustomCampaign' => 'MyCustomCampaign',
            'DeduplicateCampaign' => false,
            'CustomID' => 'MyCustomId',
            'TemplateErrorDeliver' => true,
        ], $mailer->extractMessage($message));

        $this->assertFalse($mailer->sendMessage($message));
        $this->assertNotEmpty($mailer->lastError);
    }
}
