<?php

namespace luya\mailjet\tests;

use luya\mailjet\Mjml;

class MjmlTest extends MailjetTestCase
{
    public function testArrayParser()
    {
        $this->assertSame(
            '{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[],"attributes":{"foo":"bar"},"content":"Hello World"}],"attributes":[],"content":""}', 
            Mjml::getJson('<mj-section><mj-column foo="bar">Hello World</mj-column></mj-section>')
        );
    }
    
    public function testComplexExample()
    {
        $this->assertSame(
            '{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[{"tagName":"mj-button","children":[{"tagName":"span","children":[],"attributes":{"style":"color:#212020"},"content":"Shop Now"}],"attributes":{"background-color":"#ffffff","border-radius":"3px","font-family":"Times New Roman, Helvetica, Arial, sans-serif","font-size":"18px","font-weight":"normal","inner-padding":"10px 25px","padding-bottom":"30px","padding":"10px 25px"},"content":""},{"tagName":"mj-text","children":[{"tagName":"p","children":[{"tagName":"br","children":[],"attributes":[],"content":""},{"tagName":"span","children":[],"attributes":{"style":"color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif"},"content":"purchases, offer limited to stock. Cannot be combined with any offer or promotion other than free."}],"attributes":{"style":"line-height: 16px; text-align: center; margin: 10px 0;font-size:12px;color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif"},"content":"* Offer valid on Allura purchases on 17\/29\/11 at 11:59 pm. No price adjustments on previous "}],"attributes":{"align":"left","color":"#55575d","font-family":"Arial, sans-serif","font-size":"13px","line-height":"22px","padding-bottom":"0px","padding-top":"5px","padding":"10px 25px"},"content":""}],"attributes":[],"content":""}],"attributes":{"background-color":"#000000","background-repeat":"no-repeat","text-align":"center","vertical-align":"top","padding-bottom":"40px","padding":"0 0 0 0"},"content":""}',
            Mjml::getJson('<mj-section background-color="#000000" background-repeat="no-repeat" text-align="center" vertical-align="top" padding-bottom="40px" padding="0 0 0 0">
      <mj-column>
        <mj-button background-color="#ffffff" border-radius="3px" font-family="Times New Roman, Helvetica, Arial, sans-serif" font-size="18px" font-weight="normal" inner-padding="10px 25px" padding-bottom="30px" padding="10px 25px"><span style="color:#212020">Shop Now</span></mj-button>
        <mj-text align="left" color="#55575d" font-family="Arial, sans-serif" font-size="13px" line-height="22px" padding-bottom="0px" padding-top="5px" padding="10px 25px">
          <p style="line-height: 16px; text-align: center; margin: 10px 0;font-size:12px;color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif">* Offer valid on Allura purchases on 17/29/11 at 11:59 pm. No price adjustments on previous&nbsp;<br><span style="color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif">purchases, offer limited to stock. Cannot be combined with any offer or promotion other than free.</span></p>
        </mj-text>
      </mj-column>
    </mj-section>')
        );
    }
    
    public function testWithError()
    {
        $this->assertFalse(Mjml::getArray('<mj-section></mj-column>'));
        $this->assertTrue(count(Mjml::$errors) == 1);
    }
}