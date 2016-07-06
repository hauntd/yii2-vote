<?php

namespace hauntd\vote\widgets;

use yii\web\View;
use yii\web\JsExpression;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\widgets
 */
class Vote extends BaseWidget
{
    public $jsCodeKey = 'vote';

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'class' => 'vote',
        ];
    }

    /**
     * @inherit
     */
    public function init()
    {
        parent::init();
        $this->options = array_merge($this->getDefaultOptions(), $this->options);
        $this->initJsEvents();
        $this->registerJs();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        return $this->render($this->viewName, [
            'jsCodeKey' => $this->jsCodeKey,
            'entity' => $this->entity,
            'model' => $this->model,
            'targetId' => $this->targetId,
            'userValue' => $this->userValue,
            'positive' => isset($this->aggregateModel->positive) ? $this->aggregateModel->positive : 0,
            'negative' => isset($this->aggregateModel->negative) ? $this->aggregateModel->negative : 0,
            'rating' => isset($this->aggregateModel->rating) ? $this->aggregateModel->rating : 0.0,
            'options' => $this->options,
        ]);
    }

    /**
     * Initialize with default events.
     */
    public function initJsEvents()
    {
        parent::initJsEvents();
        $selector = $this->getSelector($this->options['class']);
        if (!isset($this->jsChangeCounters)) {
            $this->jsChangeCounters = "
                if (typeof(data.success) !== 'undefined') {
                    $('$selector .vote-count').text(data.aggregate.positive - data.aggregate.negative);
                    vote.find('button').removeClass('active');
                    button.addClass('active');
                }
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
                    action = button.attr('data-action'),
                    entity = vote.attr('data-entity'),
                    target = vote.attr('data-target-id');
                jQuery.ajax({
                    url: '$this->voteUrl', type: 'POST', dataType: 'json', cache: false,
                    data: { 'VoteForm[entity]': entity, 'VoteForm[targetId]': target, 'VoteForm[action]': action },
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
