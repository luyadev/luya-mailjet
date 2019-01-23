<?php

use yii\widgets\DetailView;
/* @var array $mailjet */
/* @var string $email */
?>
<?php if (!$mailjet): ?>
    <div class="alert alert-warning">Unable to find the email address <b><?= $email; ?></b> in Mailjet registry trough API.</div>
<?php else: ?>
    <p class="lead">Contact Details</p>
    <?php foreach ($mailjet as $item): ?>
        <?= DetailView::widget(['model' => $item]); ?>
    <?php endforeach; ?>
    <p class="lead mt-5">List Subscriptions</p>
    <?php if ($lists): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>List</th>
                    <th>List Id</th>
                    <th>Subscribed</th>
                    <th>Subscription Date</th>
                </tr>
            </thead>
        <?php foreach ($lists as $list): ?>
            <tr>
                <td><?= $list['list']['Name']; ?> <small>(Total subscriptions <?= $list['list']['SubscriberCount']; ?>)</small></td>
                <td><?= $list['list']['ID']; ?></td>
                <td><?= Yii::$app->formatter->asBoolean(!$list['sub']['IsUnsub']); ?></td>
                <td><?= Yii::$app->formatter->asDatetime($list['sub']['SubscribedAt']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
<?php endif; ?>