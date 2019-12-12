<?php

namespace luya\mailjet\jobs;

use Yii;
use luya\Exception;
use yii\queue\JobInterface;

/**
 * A job to trigger a send mail.
 * 
 * Compatible with Yii 2 Queue.
 * 
 * @since 1.3.0
 */
class TemplateEmailSendJob implements JobInterface
{
    public $templateId;

    public $variables = [];

    public $recipient = [];

    public $from;

    public function execute($queue)
    {
        $mailer = Yii::$app->mailer->compose();
        ->setTemplate($this->templateId)
        ->setVariables($this->variables)
        ->setTo($this->recipient);

        if ($this->from) {
            $mailer->setFrom($this->from);
        }
        
        $send = $mailer->send();

        if (!$send) {
            throw new Exception("Unable to send E-Mail. Message: " . Yii::$app->mailer->lastError);
        }
    }
}