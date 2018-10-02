<?php

namespace luya\mailjet\tests;

use luya\testsuite\cases\WebApplicationTestCase;

class MailjetTestCase extends WebApplicationTestCase
{
    public function beforeSetup()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        parent::beforeSetup();
    }
    public function getConfigArray()
    {
        return [
            'id' => 'packagetest',
            'basePath' => __DIR__,
            'language' => 'en',
            'components' => [
                'mailjet' => [
                    'class' => 'luya\mailjet\Client',
                    'apiKey' => getenv('apikey'),
                    'apiSecret' => getenv('apisecret'),
                ],
                'mailer' => [
                    'class' => 'luya\mailjet\Mailer',
                ],
            ]
        ];
    }
}