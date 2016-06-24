<?php

/* @var $jsCodeKey string */
/* @var $entity string */
/* @var $model \yii\db\ActiveRecord */
/* @var $targetId integer */
/* @var $positive integer */
/* @var $negative integer */
/* @var $rating float */
/* @var $options array */

?>
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>" data-entity="<?= $entity ?>" data-target-id="<?= $targetId ?>">
    <button class="vote-btn vote-down" data-action="negative">
        <i class="glyphicon glyphicon-arrow-down"></i>
    </button>
    <span class="vote-count"><?= $positive - $negative ?></span>
    <button class="vote-btn vote-up" data-action="positive">
        <i class="glyphicon glyphicon-arrow-up"></i>
    </button>
<!--    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">-->
<!--        <meta itemprop="interactionCount" content="Positive: --><?//= $positive?><!--"/>-->
<!--        <meta itemprop="interactionCount" content="Negative: --><?//= $negative ?><!--"/>-->
<!--        <meta itemprop="ratingValue" content="--><?//= $rating?><!--"/>-->
<!--        <meta itemprop="ratingCount" content="--><?//= $positive + $negative?><!--"/>-->
<!--        <meta itemprop="bestRating" content="10"/>-->
<!--        <meta itemprop="worstRating" content="0"/>-->
<!--    </div>-->
</div>
