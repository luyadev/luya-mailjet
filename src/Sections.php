<?php

namespace luya\mailjet;

use Mailjet\Client;
use Mailjet\Resources;
use yii\base\BaseObject;

class Sections extends BaseObject
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
     * Create MJML Snippet (Section): https://mjml.io
     *
     * @param string $name
     * @param string $mjml See https://mjml.io
     * @throws \Exception
     */
    public function create($name, $mjml)
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
    
    /**
     * 
     * @throws \Exception
     * @return array
     */
    public function list()
    {
        $filters = [
            'OwnerType' => 'user',
            'Limit' => 100,
            'EditMode' => 3
        ];
        
        $response = $this->client->get(Resources::$Template, ['filters' => $filters]);
        
        if (!$response->success()) {
            throw new \Exception("Invalid sections list call. Wrong token or secret.");
        }
        
        return $response->getData();
    }
    
    public function delete($id)
    {
        $response = $this->client->delete(Resources::$Template, ['id' => $id]);
        
        return $response->success();
    }
}