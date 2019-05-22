<?php

namespace luya\mailjet;

use yii\mail\BaseMailer;
use yii\di\Instance;
use Mailjet\Resources;

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
    
    /**
     * @param MailerMessage $message
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

        // return
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
            //'CustomCampaign' => $message->getCustomCampaign(),
            'DeduplicateCampaign' => $message->getDeduplicateCampaign(),
            'TemplateErrorReporting' => $message->getTemplateErrorReporting() ? $message->getTemplateErrorReporting() : $this->getDefaultTemplateErrorReporting(),
        ];

        if ($message->getCustomCampaign() != ''){
            $array['CustomCampaign'] = $message->getCustomCampaign();
            //$array['DeduplicateCampaign'] = $message->getDeduplicateCampaign();
        }
        
        $errorReporting = $message->getTemplateErrorReporting() ? $message->getTemplateErrorReporting() : $this->getDefaultTemplateErrorReporting();

        if ($errorReporting) {
            $array['TemplateErrorReporting'] = $errorReporting;
            $array['TemplateErrorDeliver'] = true;
        }

        // filter null and '' values not not false (which array_filter does).
        return array_filter($array, function ($value) {
            if ($value === null || $value === '') {
                return false;
            }

            return true;
        });
    }

    public static function toMultiEmailAndName($input)
    {
        $to = (array) $input;
        $adresses = [];
        foreach ($to as $key => $value) {
            $adresses[] = static::toEmailAndName([$key => $value]);
        }

        return $adresses;
    }

    public static function toEmailAndName($input)
    {
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
