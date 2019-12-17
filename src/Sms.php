<?php

namespace luya\mailjet;

use Mailjet\Client;
use Mailjet\Resources;
use yii\base\BaseObject;

/**
 * Send Mailjet SMS
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.3
 */
class Sms extends BaseObject
{
    /**
     * @var Client
     */
    public $client;
    
    public function __construct(Client $client, array $config = [])
    {
        $this->client = $client;
        parent::__construct($config);
    }

    /**
     * Send a text message to a given number
     *
     * @param string $text The text message
     * @param string $to The number
     * @param string $from The from name
     * @return boolean Whether sending was sucessfull or not.
     */
    public function send($text, $to, $from)
    {
        $response = $this->client->post(Resources::$SmsSend, ['body' => [
            'Text' => $text,
            'To' => $to,
            'From' => $from,
        ]]);

        return $response->success();
    }
}