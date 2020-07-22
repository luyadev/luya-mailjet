<?php

namespace luya\mailjet\jobs;

use Yii;
use luya\Exception;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * A job to trigger a send mail.
 *
 * Compatible with Yii 2 Queue.
 *
 * @since 1.3.0
 */
class TemplateEmailSendJob extends BaseObject implements JobInterface
{
    /**
     * @var integer The template id in the mailjet system
     */
    public $templateId;

    /**
     * @var array A list of variables. If empty no values will be sent as variables, an empty array might end in a `'Type mismatch. Expected type "object".', 'ErrorRelatedTo' => array ( 0 => 'Messages.Variables' )` execption.
     */
    public $variables = [];

    /**
     * @var array The recipient(s) of the mail.
     */
    public $recipient = [];

    /**
     * @var string This is optional, as the from address can be set inside the transactional templates.
     */
    public $from;

    /**
     * {@inheritDoc}
     */
    public function execute($queue)
    {
        $mailer = Yii::$app->mailer->compose()
            ->setTemplate($this->templateId)
            ->setTo($this->recipient);

        if (!empty($this->variables)) {
            $mailer->setVariables($this->variables);
        }

        if ($this->from) {
            $mailer->setFrom($this->from);
        }

        $send = $mailer->send();

        if (!$send) {
            throw new Exception("Unable to send E-Mail. Message: " . Yii::$app->mailer->lastError);
        }
    }
}
