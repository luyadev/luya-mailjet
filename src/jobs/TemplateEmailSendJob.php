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
        $send = Yii::$app->mailer->compose()
            ->setFrom($this->from)
            ->setTemplate($this->templateId)
            ->setVariables($this->variables)
            ->setTo($this->recipient)
            ->send();

        if (!$send) {
            throw new Exception("Unable to send E-Mail. Message: " . Yii::$app->mailer->lastError);
        }
    }
}