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
    
    /**
     * Create MJML Snippet (Section): https://mjml.io
     * 
     * @param string $name
     * @param string $mjml See https://mjml.io
     * @throws \Exception
     */
    public function createSnippet($name, $mjml)
    {
        $json = Mjml::getArray($mjml);
        
        if (!$json) {
            throw new \Exception("Invalid MJML code template. The xml(mjml) could not be parsed correctly.");
        }
        
        $body = [
            'Name' => $name,
            'Description' => $name,
            'EditMode' => 3,
            'Locale' => 'de_DE',
            'IsStarred' => false,
            'Purposes' => ['marketing', 'transactional', 'automation'],
            'OwnerType' => 'user',
        ];
        
        $response = $this->client->post(Resources::$Template, ['body' => $body]);
        
        if (!$response->success()) {
            throw new \Exception("Unable to create Snippet on API server.");
        }
        
        $id = $response->getData()[0]['ID'];
        
        
        
        $updateBody = [
            'MJMLContent' => $json,
        ];
        
        $resp = $this->client->post(Resources::$TemplateDetailcontent, ['id' => $id, 'body' => $updateBody]);
        
        return $resp->success();
    }
}
