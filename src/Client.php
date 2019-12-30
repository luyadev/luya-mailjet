<?php

namespace luya\mailjet;

use yii\base\Component;
use Mailjet\Client as MailjetClient;
use yii\base\InvalidConfigException;

/**
 * Mailjet Component.
 *
 * @property MailjetClient $client
 * @property Contacts $contacts
 * @property Sms $sms
 * @property Sections $sections
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

    private $_contacts;

    /**
     * Get Contacts component.
     *
     * @return Contacts
     * @since 1.3.0
     */
    public function getContacts()
    {
        if ($this->_contacts === null) {
            $this->_contacts = new Contacts($this->client);
        }
        
        return $this->_contacts;
    }
    
    /**
     * Mailjet Contacts Handler.
     *
     * @return Contacts
     * @deprecated 1.3.0 will be removed in version 2.0, used getContacts() or $contacts instead.
     */
    public function contacts()
    {
        return $this->getContacts();
    }

    private $_sections;
    /**
     * Mailjet Sections component.
     *
     * @return Sections
     * @since 1.3.0
     */
    public function getSections()
    {
        if ($this->_sections === null) {
            $this->_sections = new Sections($this->client);
        }

        return $this->_sections;
    }
    
    /**
     * Mailjet Sections Handler.
     *
     * @return Sections
     * @deprecated 1.3.0 will be removed in version 2.0, used getSections() or $sections instead.
     */
    public function sections()
    {
        return $this->getSections();
    }

    private $_sms;

    /**
     * Mailjet SMS component.
     *
     * @return Sms
     * @since 1.3.0
     */
    public function getSms()
    {
        if ($this->_sms === null) {
            $this->_sms = new Sms($this->client);
        }

        return $this->_sms;
    }

    /**
     * SMS Handler
     *
     * @return Sms
     * @since 1.3.0
     * @deprecated 1.3.0 will be removed in version 2.0, used getSms() or $sms instead.
     */
    public function sms()
    {
        return $this->getSms();
    }
}
