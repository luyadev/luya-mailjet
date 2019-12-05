<?php

namespace luya\mailjet\models;

use Curl\Curl;
use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\mailjet\admin\aws\MjmlPreviewActiveWindow;
use luya\mailjet\admin\Module;
use yii\behaviors\TimestampBehavior;

/**
 * Template.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $slug
 * @property text $mjml
 * @property text $html
 * @property integer $created_at
 * @property integer $updated_at
 */
class Template extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailjet_template}}';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-mailjet-template';
    }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::class],
        ];
    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_INSERT, [$this, 'generateHtmlFromApi']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'generateHtmlFromApi']);
    }

    public function generateHtmlFromApi()
    {
        $module = Module::getInstance();
        $ch = curl_init('https://api.mjml.io/v1/render');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', $module->mjmlApiApplicationId, $module->mjmlApiSecretKey));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['mjml' => $this->mjml]));
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            throw new \RuntimeException(curl_error($ch));
        }

        $decode = json_decode($response, true);

        $this->updateAttributes(['html' => $decode['html']]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'slug' => Yii::t('app', 'Slug'),
            'mjml' => Yii::t('app', 'Mjml'),
            'html' => Yii::t('app', 'Html'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function renderWithVariables()
    {
        $vars = [];
        foreach ($this->templateVariables as $var) {
            $vars[$var->key] = $var->value;
        }

        return $this->render($vars);
    }

    public function render(array $params = [])
    {
        $html = $this->html;
        if (preg_match_all("/{{%(.*?)}}/", $this->html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (isset($params[$match[1]])) {
                    $html = str_replace($match[0], $params[$match[1]], $html);
                }
            }
        }

        return $html;
    }

    public function attributeHints()
    {
        return [
            'mjml' => 'You can define variables {{%variable}} in the mjml which can be replaced when parsing.',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mjml', 'html'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['slug'], 'string', 'max' => 150],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'slug' => 'text',
            'mjml' => 'raw',
            'html' => 'raw',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['slug']],
            [['create', 'update'], ['slug', 'mjml', 'html', 'created_at', 'updated_at']],
            ['delete', false],
        ];
    }

    public function ngRestActiveWindows()
    {
        return [
            ['class' => MjmlPreviewActiveWindow::class],
        ];
    }

    public function ngRestRelations()
    {
        return [
            ['label' => 'Variables', 'targetModel' => TemplateVariable::class, 'dataProvider' => $this->getTemplateVariables()]
        ];
    }

    public function getTemplateVariables()
    {
        return $this->hasMany(TemplateVariable::class, ['template_id' => 'id']);
    }
}