<?php

namespace luya\mailjet;

use yii\mail\BaseMessage;
use luya\Exception;
use yii\helpers\VarDumper;

/**
 * Mailjet Message.
 * 
 * Inspired by https://github.com/weluse/yii2-mailjet
 * 
 * @author Basil Suter <basil@nadar.io>
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
        $this->_from = $this->toEmailAndName($from);
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
        $this->_templateErrorReporting = $this->toEmailAndName($templateErrorReporting);
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
        $this->_sender = $this->toEmailAndName($sender);
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
        
        $this->_to = $this->toMultiEmailAndName($to);
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
        $this->_replyTo = $this->toEmailAndName($replyTo);
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
        $this->_cc = $this->toMultiEmailAndName($cc);
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
        $this->_bcc = $this->toMultiEmailAndName($bcc);
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
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        /*
         * 'Attachments' => [
                [
                    'ContentType' => "text/plain",
                    'Filename' => "test.txt",
                    'Base64Content' => "VGhpcyBpcyB5b3VyIGF0dGFjaGVkIGZpbGUhISEK"
                ]
            ]
         */
        throw new Exception('Not Implemented');
    }
    
    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        throw new Exception('Not Implemented');
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

    private function toMultiEmailAndName($input)
    {
        $to = (array) $input;
        $adresses = [];
        foreach ($to as $key => $value) {
            $adresses[] = $this->toEmailAndName([$key => $value]);
        }

        return $adresses;
    }

    private function toEmailAndName($input)
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
