<?php

use yii\widgets\DetailView;
/* @var array $mailjet */
/* @var string $email */
?>
<?php if (!$mailjet): ?>
    <div class="alert alert-warning">Unable to find the email addresse <b><?= $email; ?></b> in Mailjet registry trough API.</div>
<?php else: ?>
    <?php foreach ($mailjet as $item): ?>
        <?= DetailView::widget(['model' => $item]); ?>
    <?php endforeach; ?>
<?php endif; ?>