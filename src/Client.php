<?php

namespace luya\mailjet;

use yii\base\Component;
use Mailjet\Client as MailjetClient;
use yii\base\InvalidConfigException;

/**
 * Mailjet Component.
 *
 * @property \Mailjet\Client $client
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Client extends Component
{
    /**
     * @var string The mailjet api key.
     */
    public $apiKey;
    
    /**
     * @var string The mailjet secret key.
     */
    public $apiSecret;
    
    private $_client;
    
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        
        if (!$this->apiKey || !$this->apiSecret) {
            throw new InvalidConfigException("The apiKey and apiSecret properties can not be empty.");
        }
    }
    
    /**
     * Mailjet PHP SDK Client Library.
     * 
     * @return \Mailjet\Client
     */
    public function getClient()
    {
        if (!$this->_client) {
            $this->_client = new MailjetClient($this->apiKey, $this->apiSecret, true);
        }

        return $this->_client;
    }
    
    /**
     * Mailjet Contacts Handler.
     * 
     * @return Contacts
     */
    public function contacts()
    {
        return new Contacts($this->client);
    }
    
    /**
     * Mailjet Sections Handler.
     * 
     * @return Sections
     */
    public function sections()
    {
        return new Sections($this->client);
    }

    /**
     * SMS Handler
     *
     * @return Sms
     * @since 1.3.0
     */
    public function sms()
    {
        return new Sms($this->client);
    }
}
