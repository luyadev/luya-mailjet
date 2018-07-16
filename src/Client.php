<?php

namespace luya\mailjet;

use yii\base\Component;
use Mailjet\Client as MailjetClient;
use yii\base\InvalidConfigException;

/**
 * Mailjet Component.
 *
 * @property \Mailjet\Client $client
 * @author Basil Suter <basil@nadar.io>
 */
class Client extends Component
{
    public $apiKey;
    
    public $apiSecret;
    
    private $_client;
    
    public function init()
    {
        parent::init();
        
        if (!$this->apiKey || !$this->apiSecret) {
            throw new InvalidConfigException("The apiKey and apiSecret properties can not be empty.");
        }
    }
    
    /**
     *
     * @return \Mailjet\Client
     */
    public function getClient()
    {
        if (!$this->_client) {
            $this->_client = new MailjetClient($this->apiKey, $this->apiSecret, true, ['version' => 'v3.1']);
        }

        return $this->_client;
    }
    
    /**
     * 
     * @return \luya\mailjet\Contacts
     */
    public function contacts()
    {
        return new Contacts($this->client);
    }
}
