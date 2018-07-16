<?php
namespace luya\mailjet\tests;

use luya\testsuite\cases\WebApplicationTestCase;

class MailjetTest extends WebApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'packagetest',
            'basePath' => __DIR__,
            'language' => 'en',
            'components' => [
                'mailjet' => [
                    'class' => 'luya\mailjet\Client',
                    'apiKey' => 'xxx',
                    'apiSecret' => 'xxx',
                ],
                'mailer' => [
                    'class' => 'luya\mailjet\Mailer',
                ],
            ]
        ];
    }
    
    /*
    public function testSendMessage()
    {
        $mail = $this->app->mailer->compose()
            ->setFrom('basil@zephir.ch')
            ->setSubject('Hello!')
            ->setHtmlBody('<p>foo</p>')
            ->setTextBody('foo')
            ->setTo(['basil@nadar.io'])
            ->send();
        
        $this->assertTrue($mail);
    }
    
    public function testTemplateMessage()
    {
        $mail = $this->app->mailer->compose()
        ->setFrom('basil@zephir.ch')
        ->setSubject('Hello!')
        ->setTemplate(484590)
        ->setVariables(['nachname' => 'Lastname Value'])
        ->setTo(['basil@nadar.io'])
        ->send();
        
        $this->assertTrue($mail);
    }
    */
    
    public function testContacts()
    {
        /** @var \luya\mailjet\Client $client */
        $client = $this->app->mailjet;
        $response = $client->contacts()->add('basil+1@nadar.io', 'Basil 1')->add('basil+2@nadar.io', 'Basil 2')->sync();
        
        $this->assertTrue($response);
    }
}
