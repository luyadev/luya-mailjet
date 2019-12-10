<?php

namespace luya\mailjet\aws;

use Yii;
use luya\admin\ngrest\base\ActiveWindow;

/**
 * Contact Info Active Window.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class ContactInfoActiveWindow extends ActiveWindow
{
    /**
     * @var string The attribute from the model which should be choosen to get the address.
     */
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
        return 'Mailjet';
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

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getEmailFromModel();
    }

    /**
     * @inheritDoc
     */
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

        $subs = Yii::$app->mailjet->contacts()->subscriptions($email);

        $lists = [];
        if ($subs) {
            foreach ($subs as $sub) {
                $lists[] = [
                    'sub' => $sub,
                    'list' => Yii::$app->mailjet->contacts()->listDetail($sub['ListID']),
                ];
            }
        }
        return $this->render('index', [
            'email' => $email,
            'mailjet' => Yii::$app->mailjet->contacts()->search($email),
            'lists' => $lists,
        ]);
    }

    /**
     * Unsubscribe the given user from a list
     *
     * @param integer $listId
     * @param string $type
     * @return boolean
     */
    public function callbackHandle($listId, $type)
    {
        $response = Yii::$app->mailjet
            ->contacts()
            ->list($listId, $type)
            ->add($this->getEmailFromModel())
            ->sync();

        if ($response) {
            return $this->sendSuccess("List action was successfull.");
        }

        return $this->sendError("Error while updating the list.");
    }

    private function getEmailFromModel()
    {
        $model = $this->getModel();
        return $model->{$this->attribute};
    }
}
