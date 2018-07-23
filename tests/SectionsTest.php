<?php

namespace luya\mailjet\tests;

class SectionsTest extends MailjetTestCase
{
    public function testListSections()
    {
        $r = $this->app->mailjet->sections()->list();
        
        $this->assertTrue(is_array($r));
        
        foreach ($r as $i) {
            echo "DELETE: " . $i['ID'] . PHP_EOL;
            //$this->app->mailjet->sections()->delete($i['ID']);
        }
    }
    
    
    public function testCreate()
    {
        $this->app->mailjet->sections()->create('News Eintrag Ole was weiss ich', 
        '<mj-section>
        <mj-column>
            <mj-text>Das wär der Titel</mj-text>
        </mj-column>
        <mj-column>
            <mj-text>Text</mj-text>
        </mj-column>
</mj-section>');
    }
    
    /*
    public function testDelete()
    {
        $this->assertTrue($this->app->mailjet->sections()->delete(485016));
    }
    */
}