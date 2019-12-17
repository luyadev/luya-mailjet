<?php

namespace luya\mailjet\widgets;

use Yii;
use luya\base\DynamicModel;
use luya\base\Widget;
use luya\helpers\Json;
use luya\helpers\Url;
use luya\mailjet\Contacts;
use yii\base\InvalidConfigException;

/**
 * Subscribe Form Widget.
 * 
 * ```php
 * <?php $widget = SubscribeFormWidget::begin(['confirmTemplateId' => 123123, 'hashSecret' => '_ADD_A_RANDOM_STRING_', 'listId' => 123123]) ?>
 *     <?php if ($widget->isSent): ?>
 *         <div class="alert alert-success">A confirmation email has been sent to your address. Check your mails.</div>
 *     <?php elseif ($widget->isSubscribed): ?>
 *         <div class="alert alert-success">Thanks, your email address has been added to the subscription list.</div>
 *     <?php else: ?>
 *         <?php $form = ActiveForm::begin(); ?>
 *              <?= $form->field($widget->model, 'email'); ?>
 *              <?= Html::submitButton('Submit'); ?>
 *         <?php $form::end(); ?>
 *     <?php endif; ?>
 * <?php $widget::end(); ?>
 * ```
 * 
 * > The SubscribeFormWidget assumes the mailjet and mailer component are configured properly! Take a look at the README installation step.
 * 
 * @property DynamicModel $model
 * @property string $modelEmail
 * @property boolean $isSent
 * @property boolean $isSubscribed
 */
class SubscribeFormWidget extends Widget
{
    const MAIL_SENT_SUCCESS = 'mailSentSuccess';

    const MAIL_SUBSCRIBE_SUCCESS = 'mailSubscribeSuccess';

    /**
     * @var integer The mailjet list id where the subscribes should be added.
     */
    public $listId;

    /**
     * @var integer The mailjet template id which is used to send the confirmation email opt in step. The varibale `url` always exists in additional all model attributes are also passed to the template.
     */
    public $confirmTemplateId;

    /**
     * @var string A strong and random hash secret in a persistent format (don't regenerate on each request!), this will be used to hash the informations the user has provided.
     */
    public $hashSecret;

    /**
     * @var string The action which should be taken when the list sync is called, this can either be a force add or even a remove (to build unsubscribe forms).
     */
    public $listAction = Contacts::ACTION_ADDNOFORCE;

    /**
     * @var string The name of attribute which contains the email adresse. This attribute email value will be taken to confirm and subscribe
     */
    public $emailAttributeName = 'email';

    /**
     * @var array A list of attributes the {{luya\base\DynamicModel}} should contain.
     */
    public $attributes = ['email'];

    /**
     * @var array The validation rules for the model, each attribute in {{SubScribeFormWidget::$attributes}} must have at least one rule.
     */
    public $attributeRules = [
        [['email'], 'required'],
        [['email'], 'email'],
    ];

    /**
     * @var boolean Whether confirmation mail should be sent or not. If disabled, the email will be subscribed directly after successfull submit and validation of the form.
     */
    public $doubleOptIn = true;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();


        if (empty($this->listId) || empty($this->hashSecret)) {
            throw new InvalidConfigException("The listId and hashSecret properties can not be empty.");
        }

        if ($this->doubleOptIn && empty($this->confirmTemplateId)) {
            throw new InvalidConfigException("If double opt in is enabled, the confirmTemplateId property can not be empty.");
        }

        $this->processConfirmLink();

        if ($this->getModel()->load(Yii::$app->request->post()) && $this->getModel()->validate()) {

            if ($this->doubleOptIn) {
                $keys = [];
                foreach ($this->getModel()->attributes as $key => $value) {
                    $keys[] = "{$key}:{$value}";
                }
    
                $crypt = base64_encode(Yii::$app->security->encryptByPassword(Json::encode($this->getModel()->attributes), $this->hashSecret));
                $url = Url::appendQuery(['w' => self::getId(), 'subscribe' => $crypt], true);
    
                if ($this->sendConfirmMail($url, $this->model->attributes)) {
                    Yii::$app->session->setFlash(self::MAIL_SENT_SUCCESS);
                }
            } else {
                if ($this->addToList($this->getModelEmail(), $this->model->attributes)) {
                    Yii::$app->session->setFlash(self::MAIL_SUBSCRIBE_SUCCESS);
                }
            }
            
        }

        ob_start();
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $content = ob_get_clean();

        return $content;
    }

    private $_model;

    /**
     * Getter method for the Model
     *
     * @return DynamicModel
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = new DynamicModel($this->attributes);
            foreach ($this->attributeRules as $rule) {
                $this->_model->addRule($rule[0], $rule[1]);
            }
        }

        return $this->_model;
    }

    /**
     * Return the Model attribute email value.
     *
     * @return string The E-Mail adresse attributes value.
     */
    public function getModelEmail()
    {
        return $this->getModel()->{$this->emailAttributeName};
    }

    /**
     * Whether form has been sent or not.
     *
     * @return boolean
     */
    public function getIsSent()
    {
        return Yii::$app->session->getFlash(self::MAIL_SENT_SUCCESS);
    }

    /**
     * Whether mail confirmation has been done and user is subscribed to the list.
     *
     * @return boolean
     */
    public function getIsSubscribed()
    {
        return Yii::$app->session->getFlash(self::MAIL_SUBSCRIBE_SUCCESS);
    }

    /**
     * Watch for generic urls wich subscribe and widget infos.
     */
    public function processConfirmLink()
    {
        $widgetId = Yii::$app->request->get('w');
        $subscribe = Yii::$app->request->get('subscribe');

        if ($widgetId == self::getId() && !empty($subscribe)) {
            $data = Yii::$app->security->decryptByPassword(base64_decode($subscribe), $this->hashSecret);
            $attributes = Json::decode($data);
            $this->getModel()->attributes = $attributes;
            if ($this->getModel()->validate()) {
                if ($this->addToList($this->getModelEmail(), $this->model->attributes)) {
                    Yii::$app->session->setFlash(self::MAIL_SUBSCRIBE_SUCCESS);
                }
            }
        }
    }

    /**
     * Send the confirm mail
     *
     * @param string $url
     * @param array $variables
     * @return boolean
     */
    public function sendConfirmMail($url, array $variables = [])
    {
        return Yii::$app->mailer->compose()
            ->setTemplate($this->confirmTemplateId)
            ->setVariables(array_merge(['url' => $url], $variables))
            ->setTo($this->getModelEmail())
            ->send();
    }

    /**
     * Add the user to the mailjet conacts list
     *
     * @param string $email
     * @param array $properties
     * @return boolean
     */
    public function addToList($email, array $properties = [])
    {
        return Yii::$app->mailjet->contacts()
            ->list($this->listId, $this->listAction)
                ->add($email, $properties)
                ->sync();
    }
}