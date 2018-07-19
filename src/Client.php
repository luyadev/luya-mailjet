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
     * {"tagName":"mj-section","children":[{"tagName":"mj-column","attributes":{},"children":[{"tagName":"mj-html","content":"","attributes":{"padding":"0px"}},{"tagName":"mj-image","attributes":{"src":"http://191n.mj.am/tplimg/191n/b/040q/qz82.png","align":"center","width":"550px","height":"auto","padding-bottom":"0px","alt":"","href":"","border":"none","padding":"10px 25px","target":"_blank","border-radius":"","title":"","padding-top":"0px"}}]}],"attributes":{"background-repeat":"repeat","padding":"20px 0","background-size":"auto","padding-top":"0px","padding-bottom":"0px","background-color":"#ffffff","text-align":"center","vertical-align":"top","passport":{"version":"3.3.5"}}}
     * 
     * @param string $name
     * @param array $mjml
     * @throws \Exception
     */
    public function createSnippet($name, array $mjml)
    {
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
            throw new \Exception("Fehler!");
        }
        
        $id = $response->getData()[0]['ID'];
        
        $updateBody = [
            'MJMLContent' => $mjml,
        ];
        
        $resp = $this->client->post(Resources::$TemplateDetailcontent, ['id' => $id, 'body' => $updateBody]);
        
        return $resp->success();
    }
}
