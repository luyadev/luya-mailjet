<?php

namespace luya\mailjet;

use yii\mail\BaseMailer;
use yii\di\Instance;
use Mailjet\Resources;

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

    public $lastError;
    
    /**
     * {@inheritDoc}
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        parent::init();
        $this->mailjet = Instance::ensure($this->mailjet, 'luya\mailjet\Client');
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
        
        // create response
        $this->response = $this->mailjet->client->post(Resources::$Email, ['body' => $body], ['version' => 'v3.1']);
        
        if (!$this->response->success()) {
            $this->lastError = $this->response->getData() . ' | ' . $this->response->getBody() . ' | ' . $this->response->getReasonPhrase();
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
        ];
        
        // remove empty values from array
        return array_filter($array);
    }
}