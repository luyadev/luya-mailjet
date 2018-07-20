<?php
namespace luya\mailjet\tests;

class MailjetTest extends MailjetTestCase
{
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
        $this->assertTrue($this->app->mailjet->createSnippet('1337 ' . date("d.m.Y H:i:s"), 
            '<mj-section background-color="#000000" background-repeat="no-repeat" text-align="center" vertical-align="top" padding-bottom="40px" padding="0 0 0 0">
      <mj-column>
        <mj-button background-color="#ffffff" border-radius="3px" font-family="Times New Roman, Helvetica, Arial, sans-serif" font-size="18px" font-weight="normal" inner-padding="10px 25px" padding-bottom="30px" padding="10px 25px"><span style="color:#212020">Shop Now</span></mj-button>
        <mj-text align="left" color="#55575d" font-family="Arial, sans-serif" font-size="13px" line-height="22px" padding-bottom="0px" padding-top="5px" padding="10px 25px">
          <p style="line-height: 16px; text-align: center; margin: 10px 0;font-size:12px;color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif">* Offer valid on Allura purchases on 17/29/11 at 11:59 pm. No price adjustments on previous&nbsp;<br><span style="color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif">purchases, offer limited to stock. Cannot be combined with any offer or promotion other than free.</span></p>
        </mj-text>
      </mj-column>
    </mj-section>'));
    }
}
