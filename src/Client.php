<?php

namespace luya\mailjet;

use yii\base\Component;
use Mailjet\Client as MailjetClient;
use yii\base\InvalidConfigException;
use Mailjet\Resources;

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
    
    public function createSnippet($name, $html, $text)
    {
        $body = [
            'Name' => $name,
            'EditMode' => 3,
            'OwnerType' => 'apikey',
        ];
        
        $response = $this->client->post(Resources::$Template, ['body' => $body]);
        
        if (!$response->success()) {
            throw new \Exception("Fehler!");   
        }

        $id = $response->getData()[0]['ID'];
        
        $updateBody = [
            'Html-part' => $html,
            'Text-part' => $text,
        ];
        
        $resp = $this->client->post(Resources::$TemplateDetailcontent, ['id' => $id, 'body' => $updateBody]);
        
        return $resp->success();
    }
}
