<?php

namespace hauntd\vote\widgets;

use Yii;
use yii\bootstrap\Html;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\widgets
 */
class Favorite extends VoteToggle
{
    public $jsCodeKey = 'vote-favorite';

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return array_merge(parent::getDefaultOptions(), [
            'class' => 'vote-toggle vote-toggle-favorite',
        ]);
    }

    /**
     * @return array
     */
    public function getDefaultButtonOptions()
    {
        return array_merge(parent::getDefaultButtonOptions(), [
            'icon' => Html::icon('glyphicon glyphicon-star'),
            'label' => Yii::t('vote', 'Add to favorites'),
        ]);
    }
}
