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
                    'apiKey' => 'xx',
                    'apiSecret' => 'xx',
                ],
                'mailer' => [
                    'class' => 'luya\mailjet\Mailer',
                ],
            ]
        ];
    }
    
    public function testSendMessage()
    {
        $mail = $this->app->mailer->compose()
            ->setFrom('basil@zephir.ch')
            ->setSubject('Hello!')
            ->setHtmlBody('<p>foo <a href="https://luya.io">luya.io</a></p>')
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
    
    public function testContacts()
    {
        $client = $this->app->mailjet;
        $response = $client->contacts()
        ->list(12561)
            ->add('basil+1@nadar.io', ['firstname' => 'b1'])
            ->add('basil+2@nadar.io', ['firstname' => 'b2'])
            ->add('basil+3@nadar.io', ['firstname' => 'b3'])
            ->sync();
        
        $this->assertTrue($response);
    }
    
    public function testCreateSnippet()
    {
        $r = $this->app->mailjet->createSnippet('foo 4 - snippet x ' . time(), '<p>FOOBAR</p>', 'foo');
        
        $this->assertTrue($r);
    }
}
