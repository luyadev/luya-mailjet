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
        
        if (!$mail) {
            var_dump($this->app->mailer->lastError);
        }

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

        if (!$mail) {
            var_dump($this->app->mailer->lastError);
        }
        
        $this->assertTrue($mail);
    }
    
    public function testContacts()
    {
        $client = $this->app->mailjet;
        $randomMail = 'johndoe'.rand(0, 999999999999999).'@luya.io';
        $listId = 622;

        // as the jobs run at the same time concurrent, we need a random timeout for all requests.
        $randomTimeout = rand(1,15);

        echo "random email: " . $randomMail;

        $response = $client->contacts
        ->list($listId, Contacts::ACTION_ADDFORCE)
            ->add($randomMail)
            ->sync();
        
        $this->assertTrue($response);

        sleep(10);
        sleep($randomTimeout);

        $search = $client->contacts->search($randomMail);
        var_dump($search);
        $this->assertNotFalse($search);

        sleep(10);
        sleep($randomTimeout);

        $this->assertTrue($client->contacts->isInList($randomMail, $listId));

        // unsubscribe
        sleep(10);
        sleep($randomTimeout);

        $response = $client->contacts
        ->list($listId, Contacts::ACTION_UNSUBSCRIBE)
            ->add($randomMail)
            ->sync();

        sleep(10);
        sleep($randomTimeout);

        $this->assertFalse($client->contacts->isInList($randomMail, $listId));

        sleep(10);
        sleep($randomTimeout);

        // just remove all the data :-)
        $this->app->mailjet->contacts()
        ->list($listId, Contacts::ACTION_REMOVE)
            ->add($randomMail)
            ->sync();
    }
    public function testContactsItems()
    {
        $client = $this->app->mailjet;
        $listId = 622;
        $items = $client->contacts->items($listId);

        $this->assertNotEmpty($items);
    }
}
