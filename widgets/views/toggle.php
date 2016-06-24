<?php

use yii\helpers\Html;

/* @var $jsCodeKey string */
/* @var $entity string */
/* @var $model \yii\db\ActiveRecord */
/* @var $targetId integer */
/* @var $count integer */
/* @var $options array */
/* @var $buttonOptions array */

?>
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>"
     data-entity="<?= $entity ?>" data-target-id="<?= $targetId ?>">
    <button class="vote-btn <?= $buttonOptions['class'] ?>">
        <span class="vote-icon"><?= $buttonOptions['icon'] ?></span>
        <span class="vote-label"><?= Html::encode($buttonOptions['label']) ?></span>
        <span class="vote-count"><?= $count ?></span>
    </button>
</div>

<!--<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">-->
<!--    <meta itemprop="interactionCount" content="Positive: --><?//= $positive?><!--"/>-->
<!--    <meta itemprop="interactionCount" content="Negative: --><?//= $negative ?><!--"/>-->
<!--    <meta itemprop="ratingValue" content="--><?//= $rating?><!--"/>-->
<!--    <meta itemprop="ratingCount" content="--><?//= $positive + $negative?><!--"/>-->
<!--    <meta itemprop="bestRating" content="10"/>-->
<!--    <meta itemprop="worstRating" content="0"/>-->
<!--</div>-->
