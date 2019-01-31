<?php

namespace luya\mailjet;

use yii\base\BaseObject;
use Mailjet\Client;
use Mailjet\Resources;
use yii\base\InvalidConfigException;

/**
 * Sync contacts to lists.
 * 
 * ```php
 * $this->app->mailjet->contacts()
 *     ->list(12345)
 *         ->add('basil+1@nadar.io')
 *         ->add('basil+2@nadar.io')
 *         ->add('basil+3@nadar.io', ['firstname' => 'Basil'])
 *         ->sync();
 * ```
 * 
 * All users will be snyced to all given lists:
 * 
 * ```php
 * $this->app->mailjet->contacts()
 *     ->list(1)
 *       ->add('1@foo.com')
 *     ->list(2)
 *       ->add('2@foo.com')
 * ```
 * 
 * Now 1@foo.com and 2@foo.com are both synced to list 1 and 2.
 * 
 * In order to remove/unsubscribe contacts from a list use:
 * 
 * ```php
 * $this->app->mailjet->contacts()
 *     ->list(1234, Contacts::ACTION_REMOVE)
 *         ->add('remove1@example.com')
 *         ->add('remove2@example.com')
 *         ->sync();
 * ```
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Contacts extends BaseObject
{
    const ACTION_ADDFORCE = 'addforce';
    
    const ACTION_ADDNOFORCE = 'addnoforce';
    
    const ACTION_REMOVE = 'remove';
    
    const ACTION_UNSUBSCRIBE = 'unsub';
    
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
    
    /**
     * @param string $email
     * @param array $properties Where the key is the property, check: https://app.mailjet.com/contacts/lists/properties
     * @return \luya\mailjet\Contacts
     */
    public function add($email, array $properties = [])
    {
        $item = ['Email' => $email, 'Properties' => $properties];
        
        $this->_contacts[] = array_filter($item);

        return $this;
    }
    
    private $_lists = [];
    
    /**
     * 
     * @param integer $id
     * @param string $action
     * @return \luya\mailjet\Contacts
     */
    public function list($id, $action = self::ACTION_ADDNOFORCE)
    {
        $this->_lists[] = ['ListId' => $id, 'action' => $action];
        
        return $this;
    }
    
    /**
     * 
     * @throws InvalidConfigException
     * @return boolean
     */
    public function sync()
    {
        if (empty($this->_lists)) {
            throw new InvalidConfigException("You have to define at list one list where the contacts should be synced to. call list().");
        }
        
        $body = [
            'Contacts' => $this->_contacts,
            'ContactsLists' => $this->_lists
        ];
        
        $response = $this->client->post(Resources::$ContactManagemanycontacts, ['body' => $body]);
        
        return $response->success();
    }
    
    /**
     * Search for a given Conact.
     * 
     * @param mixed $emailOrId
     * @return array|boolean
     */
    public function search($emailOrId)
    {
        $response = $this->client->get(Resources::$Contact, ['id' => $emailOrId]);
        
        if ($response->success()) {
            return $response->getData();
        }
        
        return false;
    }

    /**
     * Check if a user is in the given list and not unsubscibred.
     * 
     * @param string|integer $emailOrId
     * @param integer $listId The list ID
     * @return boolean
     */
    public function isInList($emailOrId, $listId)
    {
        $subs = $this->subscriptions($emailOrId);

        if (!$subs) {
            return false;
        }

        foreach ($subs as $sub) {
            if ($sub['ListID'] == $listId && $sub['IsUnsub'] === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all list subscriptions for a given Contact.
     */
    public function subscriptions($emailOrId)
    {
        $response = $this->client->get(Resources::$ContactGetcontactslists, ['id' => $emailOrId]);

        if ($response->success()) {
            return $response->getData();
        }
        
        return false;
    }

    /**
     * Get details for a given list id (subscribes and name)
     *
     * @param integer $listId
     * @return array|boolean
     */
    public function listDetail($listId)
    {
        $response = $this->client->get(Resources::$Contactslist, ['id' => $listId]);

        if ($response->success()) {
            return current($response->getData());
        }
        
        return false;
    }
    
    /**
     * Get contact items.
     *
     * @param integer $listId If not porvided all contacts are returned - Retrieves only contacts that are part of this Contact List ID.
     * @param boolean $isExcludedFromCampaigns If null, this parameter has no effect, otherwise: When true, 
     * will retrieve only contacts that have been added to the exclusion list for marketing emails. When 
     * false, those contacts will be excluded from the response.
     * @return array|boolean
     */
    public function items($listId = null, $isExcludedFromCampaigns = null)
    {
        // setup limit
        $limit = 100;
        // prepare filters
        $filters = [];
        $filters['Limit'] = $limit;
        if ($listId) {
            $filters['ContactsList'] = $listId;
        }
        if ($isExcludedFromCampaigns !== null) {
            $filters['IsExcludedFromCampaigns'] = $isExcludedFromCampaigns;
        }
        
        // prepare totalAmount filter only:
        $totalFilters = $filters;
        $totalFilters['countOnly'] = true;

        $response = $this->client->get(Resources::$Contact, [
            'filters' => $totalFilters,
        ]);
        
        if (!$response->success()) {
            return false;
        }

        // prepare total count and pages
        $totalCount = $response->getCount();
        $pages = ceil($totalCount / $limit);

        $data = [];
        for ($i = 0; $i <= $pages; $i++) {
            $offset = $limit * $i;
            $filters['Offset'] = $offset;
            $response = $this->client->get(Resources::$Contact, [
                'filters' => $filters,
            ]);

            if ($response->success()) {
                $data = array_merge($data, $response->getData());
            }
        }
        
        return $data;
    }
}