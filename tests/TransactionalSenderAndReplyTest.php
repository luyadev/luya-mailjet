<?php

namespace luya\mailjet\tests;

class TransactionalSenderAndReplyTest extends MailjetTestCase
{
    public function testTemplateMessage()
    {
        $mail = $this->app->mailer->compose()
        ->setFrom('basil+othersender@nadar.io')
        ->setReplyTo(['reply@luya.io'])
        ->setSubject('Hello!')
        ->setTemplate(550255)
        ->setVariables(['nachname' => 'Lastname Value'])
        ->setTo(['basil+unittestreplytest@nadar.io'])
        //->setSender(['basil+othersender@nadar.io'])
        ->send();
        
        $this->assertTrue($mail);
    }
}
