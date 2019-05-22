<?php

namespace luya\mailjet\tests;

use luya\mailjet\Mjml;

class MjmlTest extends MailjetTestCase
{
    public function testArrayParser()
    {
        $this->assertSame(
            '{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[],"attributes":{"foo":"bar"},"content":"Hello World"}],"attributes":[]}',
            Mjml::getJson('<mj-section><mj-column foo="bar">Hello World</mj-column></mj-section>')
        );
    }
    
    public function testComplexExample()
    {
        $this->assertSame(
            '{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[{"tagName":"mj-button","children":[{"tagName":"span","children":[],"attributes":{"style":"color:#212020"},"content":"Shop Now"}],"attributes":{"background-color":"#ffffff","border-radius":"3px","font-family":"Times New Roman, Helvetica, Arial, sans-serif","font-size":"18px","font-weight":"normal","inner-padding":"10px 25px","padding-bottom":"30px","padding":"10px 25px"}},{"tagName":"mj-text","children":[],"attributes":{"align":"left","color":"#55575d","font-family":"Arial, sans-serif","font-size":"13px","line-height":"22px","padding-bottom":"0px","padding-top":"5px","padding":"10px 25px"},"content":"\n          <p style=\"line-height: 16px; text-align: center; margin: 10px 0;font-size:12px;color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif\">* Offer valid on Allura purchases on 17\/29\/11 at 11:59 pm. No price adjustments on previous <br \/><span style=\"color:#ffffff;font-family:\'Times New Roman\',Helvetica,Arial,sans-serif\">purchases, offer limited to stock. Cannot be combined with any offer or promotion other than free.<\/span><\/p>\n        "}],"attributes":[]}],"attributes":{"background-color":"#000000","background-repeat":"no-repeat","text-align":"center","vertical-align":"top","padding-bottom":"40px","padding":"0 0 0 0"}}',
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
    
    public function testFromPassport()
    {
        $this->assertSame('{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[{"tagName":"mj-image","children":[],"attributes":{"src":"http:\/\/191n.mj.am\/tplimg\/191n\/b\/040q\/qz8m.png"}}],"attributes":[]},{"tagName":"mj-column","children":[{"tagName":"mj-text","children":[],"attributes":[],"content":"<p>The Content of right Column<\/p>"}],"attributes":[]}],"attributes":{"passport":{"version":"3.3.5"}}}', Mjml::getJson('<mj-section passport="3.3.5">
        <mj-column>
            <mj-image src="http://191n.mj.am/tplimg/191n/b/040q/qz8m.png" />
        </mj-column>
        <mj-column>
            <mj-text><p>The Content of right Column</p></mj-text>
        </mj-column>
</mj-section>'));
    }
    
    public function testWithError()
    {
        $this->assertFalse(Mjml::getArray('<mj-section></mj-column>'));
        $this->assertTrue(count(Mjml::$errors) == 1);
    }

    public function testLinksInText()
    {
        $content = Mjml::getJson('
            <mj-section>
                <mj-column>
                    <mj-text>
<a href="https://luya.io?id=1">test</a></mj-text>
                </mj-column>
            </mj-section>');

        $this->assertSame('{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[{"tagName":"mj-text","children":[],"attributes":[],"content":"\n<a href=\"https:\/\/luya.io?id=1\">test<\/a>"}],"attributes":[]}],"attributes":[]}', $content);

        // with mj-text attributes:

        $content = Mjml::getJson('
            <mj-section>
                <mj-column>
                    <mj-text fonts-size="15px"><a href="https://luya.io?id=1">test</a></mj-text>
                </mj-column>
            </mj-section>');

        $this->assertSame('{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[{"tagName":"mj-text","children":[],"attributes":{"fonts-size":"15px"},"content":"<a href=\"https:\/\/luya.io?id=1\">test<\/a>"}],"attributes":[]}],"attributes":[]}', $content);
        
        

        $content = Mjml::getJson('
            <mj-section>
                <mj-column>
                    <mj-text><a href="https://luya.io?id=1">test</a></mj-text>
                </mj-column>
            </mj-section>');

        $this->assertSame('{"tagName":"mj-section","children":[{"tagName":"mj-column","children":[{"tagName":"mj-text","children":[],"attributes":[],"content":"<a href=\"https:\/\/luya.io?id=1\">test<\/a>"}],"attributes":[]}],"attributes":[]}', $content);
    }

    public function testNestedMjText()
    {
        $content = Mjml::getJson('
        <mj-section>
        <mj-text>1</mj-text>
        <mj-text>2</mj-text>
        </mj-section>');
        $this->assertContains('{"tagName":"mj-text","children":[],"attributes":[],"content":"1"}', $content);
        $this->assertContains('{"tagName":"mj-text","children":[],"attributes":[],"content":"2"}', $content);

        // test is an invalid nesting of content!

        $content = Mjml::getJson('
            <mj-section>
                <mj-column>
                    <mj-text>
                        <mj-text><a href="?">aha?</a></mj-text>
                    </mj-text>
                </mj-column>
            </mj-section>');

        $this->assertFalse($content);
    }

    public function testSameLineMultiple()
    {
        $content = Mjml::getJson('
            <mj-section>
                <mj-text>1</mj-text>
                <mj-text>1</mj-text>
            </mj-section>');

        $this->assertSame('{"tagName":"mj-section","children":[{"tagName":"mj-text","children":[],"attributes":[],"content":"1"},{"tagName":"mj-text","children":[],"attributes":[],"content":"1"}],"attributes":[]}', $content);
    }
}
