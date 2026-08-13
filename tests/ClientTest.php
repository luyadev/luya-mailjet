<?php

namespace luya\mailjet\tests;

use luya\mailjet\Client;
use luya\testsuite\cases\WebApplicationTestCase;
use Mailjet\Client as MailjetClient;
use yii\base\InvalidConfigException;

/**
 * Ensure the mailjet SDK clients can be created.
 *
 * None of the assertions below perform an api call, they only cover the client creation.
 */
class ClientTest extends WebApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'clienttest',
            'basePath' => __DIR__,
        ];
    }

    private function createComponent(array $config = [])
    {
        return new Client(array_merge([
            'apiKey' => 'unittestapikey',
            'apiSecret' => 'unittestapisecret',
            'smsKey' => 'unittestsmskey',
        ], $config));
    }

    public function testMissingCredentialsThrowsException()
    {
        $this->expectException(InvalidConfigException::class);

        new Client();
    }

    public function testGetClient()
    {
        $component = $this->createComponent();

        $this->assertInstanceOf(MailjetClient::class, $component->getClient());
    }

    public function testGetSmsClient()
    {
        $component = $this->createComponent();

        $this->assertInstanceOf(MailjetClient::class, $component->getSmsClient());
    }

    public function testClientsAreCached()
    {
        $component = $this->createComponent();

        $this->assertSame($component->getClient(), $component->getClient());
        $this->assertSame($component->getSmsClient(), $component->getSmsClient());
        $this->assertNotSame($component->getClient(), $component->getSmsClient());
    }
}
