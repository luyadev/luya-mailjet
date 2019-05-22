<?php

namespace luya\mailjet;

use yii\base\Component;
use Mailjet\Client as MailjetClient;
use yii\base\InvalidConfigException;
use Mailjet\Resources;
use luya\helpers\Html;

/**
 * Mailjet Component.
 *
 * @property \Mailjet\Client $client
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
     *
     * @return \luya\mailjet\Contacts
     */
    public function contacts()
    {
        return new Contacts($this->client);
    }
    
    /**
     *
     * @return \luya\mailjet\Sections
     */
    public function sections()
    {
        return new Sections($this->client);
    }
}
