<?php

namespace luya\mailjet;

use luya\Exception;
use yii\mail\BaseMailer;
use yii\di\Instance;
use Mailjet\Resources;
use yii\base\InvalidConfigException;

/**
 * Mailjet Mailer Class.
 *
 * Make sure to configure the mailjet component when configure mailer component:
 *
 * ```php
 * 'mailjet' => [
 *      'class' => 'luya\mailjet\Client',
 *      'apiKey' => '__KEY__',
 *      'apiSecret' => '__SECRET__',
 * ],
 * 'mailer' => [
 *     'class' => 'luya\mailjet\Mailer',
 *      'defaultTemplateErrorReporting' => 'errors@mywebsite.com',
 * ],
 * ```
 * @property string $defaultTemplateErrorReporting
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Mailer extends BaseMailer
{
    /**
     * @var \luya\mailjet\Client
     */
    public $mailjet = 'mailjet';
    
    /**
     * @var string
     */
    public $messageClass = 'luya\mailjet\MailerMessage';
    
    /**
     * @var \Mailjet\Response
     */
    public $response;

    /**
     * @var string Contains a string with the last error message from the mailer system.
     */
    public $lastError;
    
    /**
     * @var boolean The Send API v3.1 allows to run the API call in a Sandbox mode where all the validation of the payload will be done without delivering the message.
     */
    public $sandbox = false;

    /**
     * {@inheritDoc}
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        parent::init();
        $this->mailjet = Instance::ensure($this->mailjet, 'luya\mailjet\Client');
    }

    private $_defaultTemplateErrorReporting;

    /**
     * Set the `MJ-TemplateErrorReporting` variable for debug purposes.
     *
     * The value must be a valid e-mail-address. If a given templateErrorReporting is set in the mail message those values will take precedence over this value.
     *
     * @param string $templateErrorReporting
     * @return static
     * @see https://dev.mailjet.com/guides/?php#send-api-json-properties-v3
     */
    public function setDefaultTemplateErrorReporting($templateErrorReporting)
    {
        $this->_defaultTemplateErrorReporting = self::toEmailAndName($templateErrorReporting);
    }

    /**
     * Getter method
     *
     * @return string
     */
    public function getDefaultTemplateErrorReporting()
    {
        return $this->_defaultTemplateErrorReporting;
    }

    private $_bulkList = [];

    /**
     * Add new Bulk Message
     *
     * ```php
     * $message1 = new MailerMessage(...)
     * $message2 = new MailerMessage(...)
     * 
     * $mailer->addToBulk($message1);
     * $mailer->addToBulk($message2);
     * 
     * $mailer->sendBulk();
     * ```
     * @param MailerMessage $message
     * @since 1.8.0
     */
    public function addToBulk(MailerMessage $message)
    {
        $this->_bulkList[] = $message;
    }

    /**
     * Send the bulkd message
     *
     * @return boolean If false, see $lastError in Mailer component.
     * @since 1.8.0
     */
    public function sendBulk()
    {
        if (empty($this->_bulkList)) {
            throw new InvalidConfigException("The list of bulk messages can not be empty. use addToBulk().");
        }

        $body = ['Messages' => []];
        
        if ($this->sandbox) {
            $body['SandboxMode'] = true;
        }

        foreach ($this->_bulkList as $message) {
            /** @var MailerMessage $message */
            $body['Messages'][] = $this->extractMessage($message);
        }

        // create response
        $this->response = $this->mailjet->client->post(Resources::$Email, ['body' => $body], ['version' => 'v3.1']);
        
        if (!$this->response->success()) {
            $this->lastError = var_export($this->response->getData(), true) . ' | ' . var_export($this->response->getBody(), true) . ' | ' . var_export($this->response->getReasonPhrase(), true);
            return false;
        }

        return true;
    }
    
    /**
     * @param MailerMessage $message
     * @return boolean If false, see $lastError in Mailer component.
     */
    public function sendMessage($message)
    {
        $body = [
            'Messages' => [
                $this->extractMessage($message),
            ]
        ];

        if ($this->sandbox) {
            $body['SandboxMode'] = true;
        }
        
        // create response
        $this->response = $this->mailjet->client->post(Resources::$Email, ['body' => $body], ['version' => 'v3.1']);
        
        if (!$this->response->success()) {
            $this->lastError = var_export($this->response->getData(), true) . ' | ' . var_export($this->response->getBody(), true) . ' | ' . var_export($this->response->getReasonPhrase(), true);
            return false;
        }

        return true;
    }
    
    /**
     *
     * @param MailerMessage $message
     * @return array
     */
    public function extractMessage(MailerMessage $message)
    {
        $array = [
            'From' => $message->getFrom(),
            'To' => $message->getTo(),
            'Subject' => $message->getSubject(),
            'TextPart' => $message->getTextBody(),
            'HTMLPart' => $message->getHtmlBody(),
            'TemplateID' => $message->getTemplate(),
            'TemplateLanguage' => $message->getTemplateLanguage(),
            'Variables' => $message->getVariables(),
            'ReplyTo' => $message->getReplyTo(),
            'Sender' => $message->getSender(),
            'Cc' => $message->getCc(),
            'Bcc' => $message->getBcc(),
            'TemplateErrorReporting' => $message->getTemplateErrorReporting() ? $message->getTemplateErrorReporting() : $this->getDefaultTemplateErrorReporting(),
        ];

        if ($message->getCustomCampaign()) {
            $array['CustomCampaign'] = $message->getCustomCampaign();
            $array['DeduplicateCampaign'] = $message->getDeduplicateCampaign();
        }
        
        if ($message->getCustomId()) {
            $array['CustomID'] = $message->getCustomId();
        }
        
        // only enable error reporting information when using a template based message
        // @see https://github.com/luyadev/luya-mailjet/issues/12
        if ($message->getTemplate()) {
            $errorReporting = $message->getTemplateErrorReporting() ? $message->getTemplateErrorReporting() : $this->getDefaultTemplateErrorReporting();

            if ($errorReporting) {
                $array['TemplateErrorReporting'] = $errorReporting;
                $array['TemplateErrorDeliver'] = true;
            }
        }

        if ($message->attachments) {
            $array['Attachments'] = $message->attachments;
        }

        // filter null and '' values not not false (which array_filter does).
        return array_filter($array, function ($value) {
            if ($value === null || $value === '') {
                return false;
            }

            return true;
        });
    }

    /**
     * Generate Email and Name format based on input.
     *
     * @param array|string A list of recipients.
     */
    public static function toMultiEmailAndName($input)
    {
        $to = (array) $input;
        $adresses = [];
        foreach ($to as $key => $value) {
            $adresses[] = static::toEmailAndName([$key => $value]);
        }

        return $adresses;
    }

    /**
     * Generate name and email from a given input.
     *
     * @param array|string $input A string with e-mail or an array with key value where key is the email and value the name
     * @return array
     */
    public static function toEmailAndName($input)
    {
        if (empty($input)) {
            throw new Exception("An email or name must be provided and can not be empty.");
        }

        if (is_scalar($input)) {
            return ['Email' => $input, 'Name' => $input];
        }

        $key = key($input);
        $value = current($input);

        if (is_numeric($key)) {
            return ['Email' => $value, 'Name' => $value];
        }

        return ['Email' => $key, 'Name' => $value];
    }
}
