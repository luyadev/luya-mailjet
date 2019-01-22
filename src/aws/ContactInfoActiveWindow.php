<?php

namespace luya\mailjet\aws;

use Yii;
use luya\admin\ngrest\base\ActiveWindow;

/**
 * Contact Info Active Window.
 *
 * File has been created with `aw/create` command. 
 */
class ContactInfoActiveWindow extends ActiveWindow
{
    public $attribute = 'email';

    /**
     * @var string The name of the module where the ActiveWindow is located in order to finde the view path.
     */
    public $module = '@mailjet';

    /**
     * Default label if not set in the ngrest model.
     *
     * @return string The name of of the ActiveWindow. This is displayed in the CRUD list.
     */
    public function defaultLabel()
    {
        return 'Contact Info';
    }

    /**
     * Default icon if not set in the ngrest model.
     *
     * @var string The icon name from goolges material icon set (https://material.io/icons/)
     */
    public function defaultIcon()
    {
        return 'contact_mail';    
    }

    public function getTitle()
    {
        return $this->getEmailFromModel();   
    }

    public function getViewPath()
    {
        return  dirname(__DIR__) . '/views/aws/contact-info';
    }

    /**
     * The default action which is going to be requested when clicking the ActiveWindow.
     * 
     * @return string The response string, render and displayed trough the angular ajax request.
     */
    public function index()
    {
        $email = $this->getEmailFromModel();
        return $this->render('index', [
            'email' => $email,
            'mailjet' => $this->getMailjetResponse($email),
        ]);
    }

    private function getMailjetResponse($email)
    {
        return Yii::$app->mailjet->contacts()->search($email);
    }

    private function getEmailFromModel()
    {
        $model = $this->getModel();
        return $model->{$this->attribute};
    }
}