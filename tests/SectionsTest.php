<?php

namespace luya\mailjet\tests;

class SectionsTest extends MailjetTestCase
{
    public function testListSections()
    {
        $r = $this->app->mailjet->sections()->list();
        
        $this->assertTrue(is_array($r));
        
        foreach ($r as $i) {
            //echo "DELETE: " . $i['ID'] . PHP_EOL;
            $this->app->mailjet->sections()->delete($i['ID']);
        }
    }
    
    
    public function testCreate()
    {
        $this->assertNotFalse($this->app->mailjet->sections()->create(
            '[TEST SECTIONS] ' . time(),
            '<mj-section passport="3.3.5">
        <mj-column>
            <mj-text>TEST1</mj-text>
            <mj-text>TEASER1</mj-text>
        </mj-column>
        <mj-column>
            <mj-image src="https://api.heartbeat.gmbh/image/logo-heartbeat-gmbh_ea057f17.png" alt="" align="center" border="none"></mj-image>
        </mj-column>
        </mj-section>'
        ));
    }

    public function testErrorMessage()
    {
        $section = $this->app->mailjet->sections();
        $response = $section->create('failed mjml', '<mj->section>test</mj-section>');

        $this->assertFalse($response);
        $this->assertNotNull($section->getErrorMessage());
    }
}
