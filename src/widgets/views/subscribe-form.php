<?php

use luya\widgets\SubmitButtonWidget;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(); ?>
    <?php foreach($attributes as $attribute): ?>
        <?= $form->field($model, $attribute); ?>
    <?php endforeach ;?>
    <?= SubmitButtonWidget::widget(['activeForm' => $form, 'label' => 'Senden']); ?>
<?php $form::end(); ?>