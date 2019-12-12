<?php

namespace luya\mailjet\tests;

use luya\testsuite\cases\WebApplicationTestCase;

class MailjetTestCase extends WebApplicationTestCase
{
    public function beforeSetup()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__);
        $dotenv->safeLoad();

        parent::beforeSetup();
    }
    public function getConfigArray()
    {
        return [
            'id' => 'packagetest',
            'basePath' => __DIR__,
            'language' => 'en',
            'modules' => [
                'mailjetadmin' => 'luya\mailjet\admin\Module',
            ],
            'components' => [
                'mailjet' => [
                    'class' => 'luya\mailjet\Client',
                    'apiKey' => getenv('apikey'),
                    'apiSecret' => getenv('apisecret'),
                ],
                'mailer' => [
                    'class' => 'luya\mailjet\Mailer',
                    'sandbox' => true,
                ],
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ]
            ]
        ];
    }
}
