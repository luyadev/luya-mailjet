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
 *     <?php elseif ($widget->isConfirmed): ?>
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
 * @property DynamicModel $model
 * @property string $modelEmail
 * @property boolean $isSent
 * @property boolean $isConfirmed
 */
class SubscribeFormWidget extends Widget
{
    const MAIL_SENT_SUCCESS = 'mailSentSuccess';
    const MAIL_CONFIRM_SUCCESS = 'mailConfirmSuccess';

    public $listId;

    public $confirmTemplateId;

    public $hashSecret;

    

    public $listAction = Contacts::ACTION_ADDNOFORCE;

    /**
     * @var string The name of attribute which contains the email adresse. This attribute email value will be taken to confirm and subscribe
     */
    public $emailAttributeName = 'email';

    public $attributes = ['email'];

    public $attributeRules = [
        [['email'], 'required'],
        [['email'], 'email'],
    ];

    /**
     * @var string An optional route where the confirmation success message should be redirected to after success double opt int.
     */
    //public $confirmPage;

    /**
     * @var boolean Whether confirmation mail should be sent or not.
     */
    //public $doubleOptIn = true;

    public function init()
    {
        parent::init();

        if (empty($this->listId) || empty($this->confirmTemplateId) || empty($this->hashSecret)) {
            throw new InvalidConfigException("The listId, confirmTempalteId and hashSecret properties can not be empty.");
        }

        $this->processConfirmLink();

        if ($this->getModel()->load(Yii::$app->request->post()) && $this->getModel()->validate()) {
            $keys = [];
            foreach ($this->getModel()->attributes as $key => $value) {
                $keys[] = "{$key}:{$value}";
            }

            $crypt = base64_encode(Yii::$app->security->encryptByPassword(Json::encode($this->getModel()->attributes), $this->hashSecret));
            $url = Url::appendQuery(['w' => self::getId(), 'subscribe' => $crypt], true);

            if ($this->sendConfirmMail($url, $this->model->attributes)) {
                Yii::$app->session->setFlash(self::MAIL_SENT_SUCCESS);
            }
        }

        ob_start();
    }

    public function run()
    {
        $content = ob_get_clean();

        return $content;
    }

    private $_model;

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

    public function getModelEmail()
    {
        return $this->getModel()->{$this->emailAttributeName};
    }

    public function getIsSent()
    {
        return Yii::$app->session->getFlash(self::MAIL_SENT_SUCCESS);
    }

    public function getIsConfirmed()
    {
        return Yii::$app->session->getFlash(self::MAIL_CONFIRM_SUCCESS);
    }

    private function sendConfirmMail($url, array $variables = [])
    {
        return Yii::$app->mailer->compose()
            ->setTemplate($this->confirmTemplateId)
            ->setVariables(array_merge(['url' => $url], $variables))
            ->setTo($this->getModelEmail())
            ->send();
    }

    public function processConfirmLink()
    {
        $widgetId = Yii::$app->request->get('w');
        $subscribe = Yii::$app->request->get('subscribe');

        if ($widgetId == self::getId() && !empty($subscribe)) {
            $data = Yii::$app->security->decryptByPassword(base64_decode($subscribe), $this->hashSecret);
            $attributes = Json::decode($data);
            $this->getModel()->attributes = $attributes;
            if ($this->getModel()->validate()) {
                $email = $this->getModelEmail();
                if ($this->addToList($email, $this->model->attributes)) {
                    Yii::$app->session->setFlash(self::MAIL_CONFIRM_SUCCESS);
                }
            }
        }
    }

    public function addToList($email, array $properties = [])
    {
        return Yii::$app->mailjet->contacts()
            ->list($this->listId, $this->listAction)
                ->add($email, $properties)
                ->sync();
    }
}