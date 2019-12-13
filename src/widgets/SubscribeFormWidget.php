<?php

namespace luya\mailjet\widgets;

use Yii;
use luya\base\DynamicModel;
use luya\base\Widget;

class SubscribeFormWidget extends Widget
{
    public $hashSecret = 'foobar';

    public $doubleOptIn = true;

    public $confirmTemplateId;

    public $attributes = ['email'];

    public $attributeRules = [
        [['email'], 'required'],
        [['email'], 'email'],
    ];

    /**
     * @var string An optional route where the confirmation success message should be redirected to after success double opt int.
     */
    public $confirmPage;

    public function run()
    {
        $model = new DynamicModel($this->attributes);
        foreach ($this->attributeRules as $rule) {
            $model->addRule($rule[0], $rule[1]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $keys = [];
            foreach ($model->attributes as $key => $value) {
                $keys[] = "{$key}:{$value}";
            }

            $crypt = base64_encode(Yii::$app->security->encryptByPassword(implode(",", $keys), $this->hashSecret));

            var_dump($crypt);
            exit;
        }

        return $this->render('subscribe-form', [
            'model' => $model,
            'attributes' => $this->attributes,
        ]);
    }
}