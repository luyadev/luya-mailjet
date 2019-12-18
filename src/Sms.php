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
     * @param string $text The text part of the message.
     * @param string $to Message recipient. Should be between 3 and 15 characters in length. The number always starts with a plus sign followed by a country code, followed by the number. Phone numbers are expected to comply with the E.164 format.
     * @param string $from Customizable sender name. Should be between 3 and 11 characters in length, only alphanumeric characters are allowed.
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