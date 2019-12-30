<?php

namespace luya\mailjet;

use libphonenumber\PhoneNumberType;
use luya\base\DynamicModel;
use luya\validators\PhoneNumberValidator;
use Mailjet\Client;
use Mailjet\Resources;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;

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
    
    /**
     * @var string Contains the last error while sending an sms.
     */
    public $lastError;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param array $config
     */
    public function __construct(Client $client, array $config = [])
    {
        $this->client = $client;
        parent::__construct($config);
    }

    /**
     * Send a text message to a given number
     *
     * We recommend to use {{luya\validators\PhoneNumberValidator}} in order to validate the $to sms recipients number.
     *
     * @param string $text The text part of the message.
     * @param string $to Message recipient. Should be between 3 and 15 characters in length. The number always starts with a plus sign followed by a country code, followed by the number. Phone numbers are expected to comply with the E.164 format.
     * @param string $from Customizable sender name. Should be between 3 and 11 characters in length, only alphanumeric characters are allowed.
     * @param integer Whether the input data should be validated.
     * @param boolean If validate is enabled and an error occours defined whether an exception should be thrown or not.
     * @return boolean Whether sending was sucessfull or not.
     */
    public function send($text, $to, $from, $validate = false, $throwException = false)
    {
        if ($validate) {
            $model = new DynamicModel(compact('to', 'from'));
            $model->addRule(['from'], 'string', ['min' => 3, 'max' => 11]);
            $model->addRule(['to'], PhoneNumberValidator::class, ['type' => PhoneNumberType::MOBILE]);

            if (!$model->validate()) {
                if ($throwException) {
                    throw new InvalidArgumentException("The given parameters contain invalid values.");
                }

                return false;
            }
        }

        $response = $this->client->post(Resources::$SmsSend, ['body' => [
            'Text' => $text,
            'To' => $to,
            'From' => $from,
        ]]);

        $success = $response->success();

        if (!$success) {
            $this->lastError = var_export($response->getData(), true) . ' | ' . var_export($response->getBody(), true) . ' | ' . var_export($response->getReasonPhrase(), true);
        }

        return $success;
    }
}
