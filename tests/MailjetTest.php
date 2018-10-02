<?php
namespace luya\mailjet\tests;

class MailjetTest extends MailjetTestCase
{
    public function testSendMessage()
    {
        $mail = $this->app->mailer->compose()
            ->setFrom('basil+unittest@nadar.io')
            ->setSubject('Hello!')
            ->setHtmlBody('<p>foo <a href="https://luya.io">luya.io</a></p>')
            ->setTextBody('foo')
            ->setTo(['basil+unittest@nadar.io'])
            ->send();
        
        $this->assertTrue($mail);
    }
    
    public function testTemplateMessage()
    {
        $mail = $this->app->mailer->compose()
        ->setFrom('basil+unittest@nadar.io')
        ->setSubject('Hello!')
        ->setTemplate(550255)
        ->setVariables(['nachname' => 'Lastname Value'])
        ->setTo(['basil+unittest@nadar.io'])
        ->send();
        
        $this->assertTrue($mail);
    }
    
    public function testContacts()
    {
        $client = $this->app->mailjet;
        
        $response = $client->contacts()
        ->list(622)
            ->add('basil+1@nadar.io', ['firstname' => 'b1'])
            ->add('basil+2@nadar.io', ['firstname' => 'b2'])
            ->add('basil+3@nadar.io', ['firstname' => 'b3'])
            ->sync();
        
        $this->assertTrue($response);
    }
}
