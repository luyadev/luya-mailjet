<?php

namespace luya\mailjet;

use yii\base\BaseObject;
use Mailjet\Client;
use Mailjet\Resources;

class Contacts extends BaseObject
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
    
    private $_contacts = [];
    
    public function add($email, $name, array $properties = [])
    {
        $item = ['Email' => $email, 'Name' => $name, 'Properties' => $properties];
        
        $this->_contacts[] = array_filter($item);

        return $this;
    }
    
    public function sync()
    {
        $body = ['Contacts' => [$this->_contacts]];
        $response = $this->client->post(Resources::$ContactManagemanycontacts, ['body' => $body, 'id' => 12561]);
        var_dump($response->getBody(), $response->getCount(), $response->getStatus(), $response->getTotal());
        return $response->success();
    }
}