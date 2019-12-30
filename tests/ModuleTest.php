<?php

namespace luya\mailjet\tests;

use luya\testsuite\traits\MigrationFileCheckTrait;

class ModuleTest extends MailjetTestCase
{
    use MigrationFileCheckTrait;

    public function testMigrations()
    {
        $this->checkMigrationFolder('@mailjetadmin/migrations');
    }
}
