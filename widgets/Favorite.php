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
    /**
     * @var string
     */
    public $jsCodeKey = 'vote-favorite';

    /**
     * @var string
     */
    public $viewFile = 'favorite';

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
            'labelAdd' => Yii::t('vote', 'Add to favorites'),
            'labelRemove' => Yii::t('vote', 'Remove from favorites'),
        ]);
    }

    /**
     * Initialize with default events.
     * 
     * @param string $selector
     */
    public function initJsEvents($selector)
    {
        parent::initJsEvents($selector);
        $this->jsChangeCounters = "
            if (data.success) {
                $('$selector .vote-count').text(data.aggregate.positive);
                var label = '';
                if (data.toggleValue) {
                    label = button.attr('data-label-remove');
                    button.addClass('vote-active');
                } else {
                    label = button.attr('data-label-add');
                    button.removeClass('vote-active');
                }
                button.find('.vote-label').text(label);
            }
        ";
    }
}
