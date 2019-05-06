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
        $randomMail = 'johndoe'.rand(0, 999999).'@luya.io';
        $listId = 622;
        $response = $client->contacts()
        ->list($listId, Contacts::ACTION_ADDFORCE)
            ->add('basil+1@nadar.io', ['firstname' => 'b1'])
            ->add('basil+2@nadar.io', ['firstname' => 'b2'])
            ->add('basil+3@nadar.io', ['firstname' => 'b3'])
            ->add($randomMail)
            ->sync();
        
        $this->assertTrue($response);

        sleep(3);

        $this->assertNotFalse($client->contacts()->search($randomMail));

        sleep(3);

        $this->assertTrue($client->contacts()->isInList($randomMail, 622));

        // unsubscribe

        $response = $client->contacts()
        ->list($listId, Contacts::ACTION_UNSUBSCRIBE)
            ->add($randomMail)
            ->sync();

        sleep(3);

        $this->assertFalse($client->contacts()->isInList($randomMail, 622));
    }
    public function testContactsItems()
    {
        $client = $this->app->mailjet;
        $listId = 622;
        $items = $client->contacts()->items($listId);

        $this->assertNotEmpty($items);
    }
}
