<?php

namespace luya\mailjet\tests;

use luya\testsuite\cases\WebApplicationTestCase;

class MailjetTestCase extends WebApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'packagetest',
            'basePath' => __DIR__,
            'language' => 'en',
            'components' => [
                'mailjet' => [
                    'class' => 'luya\mailjet\Client',
                    'apiKey' => 'xx',
                    'apiSecret' => 'xx',
                ],
                'mailer' => [
                    'class' => 'luya\mailjet\Mailer',
                ],
            ]
        ];
    }
}