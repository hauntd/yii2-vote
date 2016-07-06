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

    public $viewName = 'toggle';
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
            'class' => 'vote-toggle btn btn-default',
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
        return $this->render($this->viewName, [
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
        parent::initJsEvents();
        $selector = $this->getSelector($this->options['class']);
        if (!isset($this->jsChangeCounters)) {
            $this->jsChangeCounters = "
                if (typeof(data.success) !== 'undefined') {
                    $('$selector .vote-count').text(data.aggregate.positive);
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
