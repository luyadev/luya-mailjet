<?php
use yii\widgets\DetailView;
use luya\admin\ngrest\aw\CallbackButtonWidget;
use luya\mailjet\Contacts;

/* @var array $mailjet */
/* @var string $email */
?>
<?php if (!$mailjet): ?>
    <div class="alert alert-warning">Unable to find the email address <b><?= $email; ?></b> in Mailjet system.</div>
<?php else: ?>
    <p class="lead">Contact</p>
    <?php foreach ($mailjet as $item): ?>
        <?= DetailView::widget(['model' => $item]); ?>
    <?php endforeach; ?>
    <p class="lead mt-5">Lists</p>
    <?php if ($lists && !empty($lists)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>List</th>
                    <th>List Id</th>
                    <th>Subscribed</th>
                    <th>Subscription Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        <?php foreach ($lists as $list): ?>
            <tr>
                <td><?= $list['list']['Name']; ?> <small>(Total subscriptions <?= $list['list']['SubscriberCount']; ?>)</small></td>
                <td><?= $list['list']['ID']; ?></td>
                <td><?= Yii::$app->formatter->asBoolean(!$list['sub']['IsUnsub']); ?></td>
                <td><?= Yii::$app->formatter->asDatetime($list['sub']['SubscribedAt']); ?></td>
                <td>
                <?php if (!$list['sub']['IsUnsub']): ?>
                <?= CallbackButtonWidget::widget([
                    'label' => 'Unsubscribe',
                    'callback' => 'handle',
                    'params' => ['listId' => $list['list']['ID'], 'type' => Contacts::ACTION_UNSUBSCRIBE],
                    'options' => ['reloadWindowOnSuccess' => true],
                ]); ?>
                <?php endif; ?>
                <?= CallbackButtonWidget::widget([
                    'label' => 'Remove',
                    'callback' => 'handle',
                    'params' => ['listId' => $list['list']['ID'], 'type' => Contacts::ACTION_REMOVE],
                    'options' => ['reloadWindowOnSuccess' => true],
                ]); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <div class="alert alert-info">The E-Mail <?= $email; ?> is not subscribed to any of your lists.</div>
    <?php endif; ?>
<?php endif; ?>