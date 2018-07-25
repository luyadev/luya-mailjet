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
            <mj-image src="http://191n.mj.am/tplimg/191n/b/040q/qz8m.png" />
        </mj-column>
        <mj-column>
            <mj-text><p>The Content of right Column</p></mj-text>
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