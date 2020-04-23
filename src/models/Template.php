<?php

namespace luya\mailjet\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\mailjet\admin\aws\MjmlPreviewActiveWindow;
use luya\mailjet\admin\Module;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;

/**
 * Template.
 *
 * @property integer $id
 * @property string $slug
 * @property text $mjml
 * @property text $html
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @since 1.3.0
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

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::class],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_INSERT, [$this, 'generateAndUpdateHtml']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'generateAndUpdateHtml']);
    }

    /**
     * Converts the mjml data into a template trough mjml API.
     *
     * @return The html content based on the mjml variable.
     */
    protected function generateAndUpdateHtml()
    {
        $html = self::parseMjmlToHtml($this->mjml);

        // ensure response contains html json key
        if ($html) {
            // update the html variable with html content
            return $this->updateAttributes(['html' => $html]);
        }

        $this->addError('html', "Either the api.mjml.io has an error or the input data is wrong.");
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

    /**
     * {@inheritDoc}
     */
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
            'slug' => 'slug',
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
            [['create', 'update'], ['slug', 'mjml']],
            ['delete', false],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function ngRestActiveWindows()
    {
        return [
            ['class' => MjmlPreviewActiveWindow::class],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function ngRestRelations()
    {
        return [
            ['label' => 'Variables', 'targetModel' => TemplateVariable::class, 'dataProvider' => $this->getTemplateVariables()]
        ];
    }

    /**
     * Get template variable relation.
     *
     * @return TemplateVariable[]
     */
    public function getTemplateVariables()
    {
        return $this->hasMany(TemplateVariable::class, ['template_id' => 'id']);
    }

    /**
     * Find a template by its slug and retutn the HTML with template params.
     *
     * @param string $slug The slug of the template
     * @param array $params An array with key values to replace.
     * @return string THe html with variables.
     */
    public static function renderHtml($slug, array $params = [])
    {
        $template = self::findOne(['slug' => $slug]);

        if (!$template) {
            throw new InvalidArgumentException("Unable to find the given template.");
        }

        return $template->render($params);
    }

    /**
     * Find Template by slug and renders the MJML template for the given variables.
     *
     * @param string $slug The slug of the template
     * @param array $params A list of params to replace wihtin the html content, variables are declared in curly brackets with a leading percent sign.
     * @return string The rendered mjml content.
     * @since 1.4.0
     */
    public static function renderMjml($slug, array $params = [])
    {
        $template = self::findOne(['slug' => $slug]);

        if (!$template) {
            throw new InvalidArgumentException("Unable to find the given template.");
        }

        return $template->parseTemplate($template->mjml, $params);
    }

    /**
     * Render the current template with all defined variables in the relation.
     *
     * @return string Returns the html with variables, if any.
     */
    public function renderWithVariables()
    {
        $vars = [];
        foreach ($this->templateVariables as $var) {
            $vars[$var->key] = $var->value;
        }

        return $this->render($vars);
    }

    /**
     * Parse variables inside a given template
     *
     * @param string $template
     * @param array $params
     * @return string
     * @since 1.4.0
     */
    public function parseTemplate($template, array $params = [])
    {
        preg_match_all("/{{%(.*?)}}/", $template, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $template;
        }

        foreach ($matches as $match) {
            if (isset($params[$match[1]])) {
                $template = str_replace($match[0], $params[$match[1]], $template);
            }
        }

        return $template;
    }

    /**
     * Render the current HTML template for the given params.
     *
     * Assuming {{%foo}} is used in the mjml tmeplate, the param to replace would be:
     *
     * ```
     * render(['foo' => 'bar']);
     * ```
     *
     * @param array $params A list of params to replace wihtin the html content, variables are declared in curly brackets with a leading percent sign.
     * @return string The rendered html with replaced variables.
     */
    public function render(array $params = [])
    {
        return $this->parseTemplate($this->html, $params);
    }



    /**
     * Generate and return the HTML data from the API.
     *
     * @return string
     * @since 1.4.0
     */
    public function generateHtml()
    {
        return self::parseMjmlToHtml($this->mjml);
    }

    /**
     * Request the HTML for a given MJML section.
     *
     * @param string $mjml The mjml data to parse into HTML.
     * @return string The parsed HTML
     * @since 1.4.0
     */
    public static function parseMjmlToHtml($mjml)
    {
        $module = Module::getInstance();
        $ch = curl_init('https://api.mjml.io/v1/render');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', $module->mjmlApiApplicationId, $module->mjmlApiSecretKey));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['mjml' => $mjml]));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \RuntimeException(curl_error($ch));
        }
        // decode response
        $decode = json_decode($response, true);
        // ensure response contains html json key
        if (isset($decode['html'])) {
            return $decode['html'];
        }

        return false;
    }
}
