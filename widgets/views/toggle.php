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

?>
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>"
     data-entity="<?= $entity ?>" data-target-id="<?= $targetId ?>">
    <button class="vote-btn <?= $buttonOptions['class'] ?>  <?= $userValue === Vote::VOTE_POSITIVE ? 'active' : '' ?>">
        <span class="vote-icon"><?= $buttonOptions['icon'] ?></span>
        <span class="vote-label"><?= Html::encode($buttonOptions['label']) ?></span>
        <span class="vote-count"><?= $count ?></span>
    </button>
</div>
