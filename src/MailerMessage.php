<?php

namespace luya\mailjet;

use yii\mail\BaseMessage;
use luya\Exception;
use luya\helpers\ArrayHelper;
use luya\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * Mailjet Message.
 *
 * Inspired by https://github.com/weluse/yii2-mailjet
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class MailerMessage extends BaseMessage
{
    private $_charset;
    
    private $_from;
    
    private $_to;
    
    private $_replyTo;
    
    private $_cc;
    
    private $_bcc;
    
    private $_subject;
    
    private $_textBody;
    
    private $_htmlBody;

    private $_customCampaign;

    private $_customId;

    private $_deduplicateCampaign = true;
    
    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->_charset;
    }
    
    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }
    
    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->_from;
    }
    
    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->_from = Mailer::toEmailAndName($from);
        return $this;
    }

    private $_templateErrorReporting;

    /**
     * Set the `MJ-TemplateErrorReporting` variable for debug purposes.
     *
     * The value must be a valid e-mail-address:
     *
     * @param string $templateErrorReporting
     * @return static
     * @see https://dev.mailjet.com/guides/?php#send-api-json-properties-v3
     */
    public function setTemplateErrorReporting($templateErrorReporting)
    {
        $this->_templateErrorReporting = Mailer::toEmailAndName($templateErrorReporting);
        return $this;
    }

    /**
     * Getter method
     *
     * @return string
     */
    public function getTemplateErrorReporting()
    {
        return $this->_templateErrorReporting;
    }

    private $_sender;

    /**
     * Change sender:
     *
     * > Your account is not authorized to use the "Sender" header. Please contact our support team to be granted permission.
     *
     * @param string|array $sender
     * @return static
     */
    public function setSender($sender)
    {
        $this->_sender = Mailer::toEmailAndName($sender);
        return $this;
    }

    /**
     * Get Sender
     *
     * @return string|array
     */
    public function getSender()
    {
        return $this->_sender;
    }

    private $_template;

    /**
     * Get template id
     *
     * @return integer
     */
    public function getTemplate()
    {
        return $this->_template;
    }
    
    /**
     * Set the template id from mailjet.
     *
     * > Transactional Templates
     *
     * @param integer $id
     * @return \luya\mailjet\MailerMessage
     * @return static
     */
    public function setTemplate($id)
    {
        $this->_template = (int) $id;
        $this->_templateLanguage = true;
        
        return $this;
    }
    
    private $_variables;
    
    /**
     * Set variables to a template.
     *
     * Where key is the variable name.
     *
     * @param array $vars
     * @return \luya\mailjet\MailerMessage
     * @return static
     */
    public function setVariables(array $vars)
    {
        foreach ($vars as $k => $v) {
            if (is_null($v) || is_bool($v) || empty($v)) {
                $vars[$k] = '';
            }

            $vars[$k] = is_array($v) ? $v : (string) $v;
        }
        $this->_variables = $vars;
        
        return $this;
    }
    
    public function getVariables()
    {
        return $this->_variables;
    }
    
    private $_templateLanguage;
    
    public function getTemplateLanguage()
    {
        return $this->_templateLanguage;
    }

    /**
     * Set TemplateLanguage
     *
     * @see https://dev.mailjet.com/guides/#use-templating-language
     * @since 1.2.0
     * @return static
     */
    public function setTemplateLanguage($value)
    {
        $this->_templateLanguage = $value;
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->_to;
    }
    
    /**
     * @inheritdoc
     * $to = 'foo@bar.com';
     * $to = ['foo@bar.com', 'quix@baz.com'];
     * $to = ['foo@bar.com' => 'John Doe'];
     */
    public function setTo($to)
    {
        $this->_to = Mailer::toMultiEmailAndName($to);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->_replyTo;
    }
    
    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->_replyTo = Mailer::toEmailAndName($replyTo);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->_cc;
    }
    
    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->_cc = Mailer::toMultiEmailAndName($cc);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->_bcc;
    }
    
    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->_bcc = Mailer::toMultiEmailAndName($bcc);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->_subject;
    }
    
    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }
    
    /**
     * return the plain text for the mail
     */
    public function getTextBody()
    {
        return $this->_textBody;
    }
    
    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->_textBody = $text;
        return $this;
    }
    
    /**
     * return the html text for the mail
     */
    public function getHtmlBody()
    {
        return $this->_htmlBody;
    }
    
    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->_htmlBody = $html;
        return $this;
    }

    /**
     * Get Custom Campaign
     *
     * @see https://dev.mailjet.com/guides/?shell#group-into-a-campaign
     * @since 1.2.0
     */
    public function getCustomCampaign()
    {
        return $this->_customCampaign;
    }

    /**
     * Set Custom Campaign
     *
     * @see https://dev.mailjet.com/guides/?shell#group-into-a-campaign
     * @param string $campaign
     * @return static
     * @since 1.2.0
     */
    public function setCustomCampaign($campaign)
    {
        $this->_customCampaign = $campaign;
        return $this;
    }
 
    /**
     * Get the CustomId
     *
     * With the Custom ID you can group events, if the Custom ID is provided at send time
     *
     * @see https://dev.mailjet.com/guides/?shell#event-api-real-time-notifications
     * @since 1.2.0
     */
    public function getCustomId()
    {
        return $this->_customId;
    }

    /**
     * Set the CustomId
     *
     * With the Custom ID you can group events, if the Custom ID is provided at send time
     *
     * @see https://dev.mailjet.com/guides/?shell#event-api-real-time-notifications
     * @param string $customid
     * @return static
     * @since 1.2.0
     */
    public function setCustomId($customId)
    {
        $this->_customId = $customId;
        return $this;
    }

    /**
     * Get DeduplicateCampaign
     *
     * @see https://dev.mailjet.com/guides/#group-into-a-campaign
     * @since 1.2.0
     */
    public function getDeduplicateCampaign()
    {
        return $this->_deduplicateCampaign;
    }

    /**
     * Set DeduplicateCampaign
     *
     * > This will only has an effect if CustomCompain is used.
     *
     * @see https://dev.mailjet.com/guides/#group-into-a-campaign
     * @param boolean $deduplicate
     * @return static
     * @since 1.2.0
     */
    public function setDeduplicateCampaign($deduplicate)
    {
        $this->_deduplicateCampaign = $deduplicate;
        return $this;
    }

    public $attachments = [];

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $this->attachments[] = [
            'ContentType' => ArrayHelper::getValue($options, 'contentType', FileHelper::getMimeType($fileName)),
            'Filename' => ArrayHelper::getValue($options, 'fileName', basename($fileName)),
            'Base64Content' => base64_encode(FileHelper::getFileContent($fileName)),
        ];

        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        $this->attachments[] = [
            'ContentType' => ArrayHelper::getValue($options, 'contentType', 'text/plain'),
            'Filename' => ArrayHelper::getValue($options, 'fileName', 'attachment.txt'),
            'Base64Content' => base64_encode($content),
        ];

        return $this;
    }
    
    
    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        throw new Exception('Not Implemented');
    }
    
    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        throw new Exception('Not Implemented');
    }
    
    /**
     * @inheritdoc
     */
    public function toString()
    {
        return VarDumper::dumpAsString($this->getTo(), 10, false). "\n"
            . $this->getSubject() . "\n"
                . $this->getTextBody();
    }
}
