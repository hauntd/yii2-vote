<?php

namespace hauntd\vote\widgets;

use Yii;
use hauntd\vote\assets\VoteAsset;
use hauntd\vote\behaviors\VoteBehavior;
use hauntd\vote\models\VoteAggregate;
use hauntd\vote\traits\ModuleTrait;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\widgets
 */
abstract class BaseWidget extends Widget
{
    use ModuleTrait;

    /**
     * @var string
     */
    public $entity;

    /**
     * @var null|\yii\db\ActiveRecord
     */
    public $model;

    /**
     * @var null|integer;
     */
    public $targetId;

    /**
     * @var string
     */
    public $voteUrl;

    /**
     * @var null|\hauntd\vote\models\VoteAggregate
     */
    public $aggregateModel;

    /**
     * @var null|integer
     */
    public $userValue;

    /**
     * @var string
     */
    public $jsBeforeVote;

    /**
     * @var string
     */
    public $jsAfterVote;

    /**
     * @var string
     */
    public $jsCodeKey = 'vote';

    /**
     * @var string
     */
    public $jsErrorVote;

    /**
     * @var string
     */
    public $jsShowMessage;

    /**
     * @var string
     */
    public $jsChangeCounters;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var string
     */
    public $viewFile = 'vote';

    /**
     * @var array
     */
    public $viewParams = [];

    /**
     * @var bool
     */
    protected $_behaviorIncluded;

    /**
     * @return string
     */
    public function getSelector()
    {
        $classes = str_replace(' ', '.', $this->options['class']);
        return ".{$classes}[data-entity=\"' + entity + '\"][data-target-id=\"' + target  + '\"]";
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!isset($this->entity) || !isset($this->model)) {
            throw new InvalidParamException(Yii::t('vote', 'Entity and model must be set.'));
        }

        $this->initDefaults();

        if ($this->getModule()->registerAsset) {
            $this->view->registerAssetBundle(VoteAsset::className());
        }
    }

    /**
     * Initialize widget with default options.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function initDefaults()
    {
        $this->voteUrl = isset($this->voteUrl) ?: Yii::$app->getUrlManager()->createUrl(['vote/default/vote']);
        $this->targetId = isset($this->targetId) ?: $this->model->getPrimaryKey();

        if (!isset($this->aggregateModel)) {
            $this->aggregateModel = $this->isBehaviorIncluded() ?
                $this->model->getVoteAggregate($this->entity) :
                VoteAggregate::findOne([
                    'entity' => $this->getModule()->encodeEntity($this->entity),
                    'target_id' => $this->targetId,
                ]);
        }

        if (!isset($this->userValue)) {
            $this->userValue = $this->isBehaviorIncluded() ? $this->model->getUserValue($this->entity) : null;
        }
    }

    /**
     * Registers jQuery handler.
     */
    protected function registerJs()
    {
        $jsCode = new JsExpression("
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
        $this->view->registerJs($jsCode, View::POS_END, $this->jsCodeKey);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function getViewParams(array $params)
    {
        return array_merge($this->viewParams, $params);
    }

    /**
     * @return bool
     */
    protected function isBehaviorIncluded()
    {
        if (isset($this->_behaviorIncluded)) {
            return $this->_behaviorIncluded;
        }

        if (!isset($this->aggregateModel) || !isset($this->userValue)) {
            foreach ($this->model->getBehaviors() as $behavior) {
                if ($behavior instanceof VoteBehavior) {
                    return $this->_behaviorIncluded = true;
                }
            }
        }

        return $this->_behaviorIncluded = false;
    }
}
