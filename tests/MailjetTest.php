<?php
namespace luya\mailjet\tests;

use luya\mailjet\Contacts;


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
        ->list(622, Contacts::ACTION_ADDFORCE)
            ->add('basil+1@nadar.io', ['firstname' => 'b1'])
            ->add('basil+2@nadar.io', ['firstname' => 'b2'])
            ->add('basil+3@nadar.io', ['firstname' => 'b3'])
            ->add('johndoe@luya.io')
            ->sync();
        
        $this->assertTrue($response);

        $this->assertNotFalse($client->contacts()->search('johndoe@luya.io'));

        sleep(2);

        $this->assertTrue($client->contacts()->isInList('johndoe@luya.io', 622));

        // unsubscribe

        $response = $client->contacts()
        ->list(622, Contacts::ACTION_UNSUBSCRIBE)
            ->add('johndoe@luya.io')
            ->sync();

        sleep(2);

        $this->assertFalse($client->contacts()->isInList('johndoe@luya.io', 622));
    }
}
