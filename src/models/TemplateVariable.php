<?php

namespace luya\mailjet\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

/**
 * Template Variable.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property integer $template_id
 * @property string $key
 * @property text $value
 */
class TemplateVariable extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailjet_template_variable}}';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-mailjet-templatevariable';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'template_id' => Yii::t('app', 'Template ID'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id'], 'required'],
            [['template_id'], 'integer'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'template_id' => 'number',
            'key' => 'text',
            'value' => 'textarea',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['template_id', 'key', 'value']],
            [['create', 'update'], ['template_id', 'key', 'value']],
            ['delete', false],
        ];
    }
}