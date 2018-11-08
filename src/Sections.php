<?php

namespace luya\mailjet;

use Mailjet\Client;
use Mailjet\Resources;
use yii\base\BaseObject;

/**
 * Create a Section Snippet.
 * 
 * ```php
 * $client = new \luya\mailjet\Client();
 * $section = new Sections($client);
 * 
 * $response = $section->create("My section", $mjmlTemplate);
 * 
 * if (!$response) {
 *     var_dump($section->getErrorMessage());
 * }
 * ```
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Sections extends BaseObject
{
    /**
     * @var Client
     */
    public $client;
    
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

    private $_errorMessage;

    /**
     * Get the error message from create() method.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }
    
    /**
     * Create MJML Snippet (Section): https://mjml.io
     *
     * If failed, return error message from getErrorMessage().
     * 
     * @param string $name The name of the template
     * @param string $mjml See https://mjml.io
     * @return boolean|integer returns the id of the template or false otherwise.
     */
    public function create($name, $mjml)
    {
        $this->_errorMessage = false;

        $json = Mjml::getArray($mjml);
        
        if (!$json) {
            $this->_errorMessage = var_export(Mjml::$errors, true);
            return false;
        }
        
        $body = [
            'Name' => $name,
            'Description' => $name,
            'EditMode' => 3,
            'Locale' => 'de_DE',
            'IsStarred' => false,
            'OwnerType' => 'apikey',
        ];
        
        $response = $this->client->post(Resources::$Template, ['body' => $body]);
        
        if (!$response->success()) {
            $this->_errorMessage = "Unable to create Snippet on API server: " . $response->getReasonPhrase() . ' (Status: ' . $response->getStatus().') (Raw Body: '.var_export($response->getBody(), true).')';
            return false;
        }
        
        $id = $response->getData()[0]['ID'];
        
        $updateBody = [
            'MJMLContent' => $json,
        ];
        
        $resp = $this->client->post(Resources::$TemplateDetailcontent, ['id' => $id, 'body' => $updateBody]);
        
        if ($resp->success()) {
            return $id;
        }

        return false;
    }
    
    /**
     * List all sections (paginated by 100 elements).
     *
     * @return array
     */
    public function list()
    {
        $filters = [
            'OwnerType' => 'apikey',
            'Limit' => 100,
            'EditMode' => 3
        ];
        
        $response = $this->client->get(Resources::$Template, ['filters' => $filters]);
        
        if (!$response->success()) {
            throw new \Exception("Invalid sections list call. Wrong token or secret.");
        }
        
        return $response->getData();
    }
    
    /**
     * Delete the given section id.
     *
     * @param integer $id
     * @return boolean
     */
    public function delete($id)
    {
        $response = $this->client->delete(Resources::$Template, ['id' => $id]);
        
        return $response->success();
    }
}