# Customization

## Custom CSS classes

```php
/** @var $model \yii\db\ActiveRecord */
<?= \hauntd\vote\widgets\Favorite::widget([
    'entity' => 'itemFavorite',
    'model' => $model,
    'options' => [
        // add extra class to widget. NOTE, that .vote and .vote-toggle-favorite classes are required
        'class' => 'vote vote-toggle-favorite vote-favorite-material',
    ],
    'buttonsOptions' => [
        // add extra class to button. NOTE, that .vote-btn class is required
        'class' => 'vote-btn button button-primary',
    ]
 ]); ?>
```

## Custom favorite/like widget icon and label

```php
/** @var $model \yii\db\ActiveRecord */
<?= \hauntd\vote\widgets\Favorite::widget([
    'entity' => 'itemFavorite',
    'model' => $model,
    'buttonsOptions' => [
        'icon' => '<i class="glyphicon glyphicon-floppy-disk"></i>',
        'label' => Yii::t('app', 'Save'),
        'labelAdd' => Yii::t('app', 'Save'),
        'labelRemove' => Yii::t('app', 'Remove'),
    ]
 ]); ?>
```

## Change widget's view entirely

Imagine that you need to:

- change favorite button's icon and label ("Save/Remove")
- change view to your own ('@app/views/vote/save.php')
- bypass additional data to custom view
- remove counter

```php
/** @var $model \yii\db\ActiveRecord */
<?= \hauntd\vote\widgets\Favorite::widget([
    'entity' => 'itemFavorite',
    'model' => $model,
    'viewFile' => '@app/views/vote/save', // YOUR WIDGET VIEW
    'viewParams' => ['document' => $model->document], // ADDITIONAL VIEW PARAMS
    'buttonsOptions' => [
        'icon' => '<i class="glyphicon glyphicon-floppy-disk"></i>',
        'labelAdd' => Yii::t('app', 'Save'),
        'labelRemove' => Yii::t('app', 'Remove'),
    ]
 ]); ?>
```

`app/views/vote/save.php`:

```php
<?php

use hauntd\vote\models\Vote;
use yii\helpers\Html;

/* @var $jsCodeKey string */
/* @var $entity string */
/* @var $model \yii\db\ActiveRecord */
/* @var $targetId integer */
/* @var $userValue null|integer */
/* @var $count integer */
/* @var $options array */
/* @var $buttonOptions array */

/* @var $document \app\models\Document */

?>
<div class="<?= $options['class'] ?>"
     data-rel="<?= $jsCodeKey ?>"
     data-entity="<?= $entity ?>"
     data-target-id="<?= $targetId ?>"
     data-user-value="<?= $userValue ?>">
    <span class="file-name">
        <?= Html::encode($document->filename) ?>
    </span>
    <button class="vote-btn <?= $buttonOptions['class'] ?> <?= $userValue === Vote::VOTE_POSITIVE ? 'vote-active' : '' ?>"
        data-label-add="<?= Html::encode($buttonOptions['labelAdd']) ?>"
        data-label-remove="<?= Html::encode($buttonOptions['labelRemove']) ?>"
        data-action="toggle">
        <span class="vote-icon"><?= $buttonOptions['icon'] ?></span>
        <span class="vote-label">
            <?= Html::encode($buttonOptions[$userValue == Vote::VOTE_POSITIVE ? 'labelRemove' : 'labelAdd']) ?>
        </span>
    </button>
</div>
```
