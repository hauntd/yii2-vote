<?php

namespace hauntd\vote\widgets;

use yii\bootstrap\Html;
use yii\web\View;
use yii\web\JsExpression;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\widgets
 */
class VoteToggle extends BaseWidget
{
    /**
     * @var string
     */
    public $jsCodeKey = 'vote-toggle';

    /**
     * @var string
     */
    public $viewFile = 'toggle';

    /**
     * @var array
     */
    public $buttonOptions = [];

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'class' => 'vote-toggle',
        ];
    }

    /**
     * @return array
     */
    public function getDefaultButtonOptions()
    {
        return [
            'class' => 'vote-btn btn btn-default',
            'icon' => Html::icon('glyphicon glyphicon-arrow-up'),
            'label' => Yii::t('vote', 'Vote up'),
        ];
    }

    public function init()
    {
        parent::init();
        $this->options = array_merge($this->getDefaultOptions(), $this->options);
        $this->buttonOptions = array_merge($this->getDefaultButtonOptions(), $this->buttonOptions);
        $this->initJsEvents();
        $this->registerJs();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        return $this->render($this->viewFile, [
            'jsCodeKey' => $this->jsCodeKey,
            'entity' => $this->entity,
            'model' => $this->model,
            'targetId' => $this->targetId,
            'userValue' => $this->userValue,
            'count' => isset($this->aggregateModel->positive) ? $this->aggregateModel->positive : 0,
            'options' => $this->options,
            'buttonOptions' => $this->buttonOptions,
        ]);
    }

    /**
     * Initialize with default events.
     */
    public function initJsEvents()
    {
        $selector = $this->getSelector($this->options['class']);
        if (!isset($this->jsChangeCounters)) {
            $this->jsChangeCounters = "
                if (typeof(data.success) !== 'undefined') {
                    $('$selector .vote-count').text(data.aggregate.positive);
                    if (data.toggleValue) {
                        button.addClass('vote-active');
                    } else {
                        button.removeClass('vote-active');
                    }
                }
            ";
        }
        if (!isset($this->jsBeforeVote)) {
            $this->jsBeforeVote = "
                $('$selector .vote-btn').prop('disabled', 'disabled').addClass('vote-loading');
                $('$selector .vote-btn').append('<div class=\"vote-loader\"><span></span><span></span><span></span></div>');
            ";
        }
        if (!isset($this->jsAfterVote)) {
            $this->jsAfterVote = "
                $('$selector .vote-btn').prop('disabled', false).removeClass('vote-loading');
                $('$selector .vote-btn .vote-loader').remove();
            ";
        }
    }

    /**
     * Registers jQuery handler.
     */
    protected function registerJs()
    {
        $js = new JsExpression("
            $('body').on('click', '[data-rel=\"{$this->jsCodeKey}\"] button', function(event) {
                var vote = $(this).closest('[data-rel=\"{$this->jsCodeKey}\"]'),
                    button = $(this),
                    entity = vote.attr('data-entity'),
                    target = vote.attr('data-target-id');
                jQuery.ajax({
                    url: '$this->voteUrl', type: 'POST', dataType: 'json', cache: false,
                    data: { 'VoteForm[entity]': entity, 'VoteForm[targetId]': target, 'VoteForm[action]': 'toggle' },
                    beforeSend: function(jqXHR, settings) { $this->jsBeforeVote },
                    success: function(data, textStatus, jqXHR) { $this->jsChangeCounters $this->jsShowMessage },
                    complete: function(jqXHR, textStatus) { $this->jsAfterVote },
                    error: function(jqXHR, textStatus, errorThrown) { $this->jsErrorVote }
                });
            });
        ");
        $this->view->registerJs($js, View::POS_END, $this->jsCodeKey);
    }
}
