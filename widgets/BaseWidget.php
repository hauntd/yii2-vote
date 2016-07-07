<?php

namespace hauntd\vote\widgets;

use hauntd\vote\assets\VoteAsset;
use hauntd\vote\models\VoteAggregate;
use hauntd\vote\traits\ModuleTrait;
use yii\base\InvalidParamException;
use yii\base\Widget;
use Yii;

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
     * @var \yii\base\Model|\yii\db\ActiveRecord
     */
    public $model;

    /**
     * @var integer;
     */
    public $targetId;

    /**
     * @var string
     */
    public $voteUrl;

    /**
     * @var \hauntd\vote\models\VoteAggregate
     */
    public $aggregateModel;

    /**
     * @var null|integer
     */
    public $userValue = null;

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
     * @param $classes
     * @return string
     */
    public function getSelector($classes)
    {
        $classes = str_replace(' ', '.', $classes);
        return ".{$classes}[data-entity=\"' + entity + '\"][data-target-id=\"' + target  + '\"]";
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!isset($this->entity)) {
            throw new InvalidParamException(Yii::t('vote', 'Entity must be set.'));
        }
        if (!isset($this->model)) {
            throw new InvalidParamException(Yii::t('vote', 'Model must be set.'));
        }
        if (!isset($this->voteUrl)) {
            $this->voteUrl = Yii::$app->getUrlManager()->createUrl(['vote/default/vote']);
        }
        if (!isset($this->targetId)) {
            $this->targetId = $this->model->getPrimaryKey();
        }
        if (!isset($this->aggregateModel)) {
            $this->aggregateModel = VoteAggregate::findOne([
                'entity' => $this->getModule()->encodeEntity($this->entity),
                'target_id' => $this->targetId,
            ]);
        }

        $this->view->registerAssetBundle(VoteAsset::class);
    }

    /**
     * Initialize with default events.
     */
    public function initJsEvents()
    {
        $selector = $this->getSelector($this->options['class']);
        if (!isset($this->jsBeforeVote)) {
            $this->jsBeforeVote = "
                $('$selector button').prop('disabled', 'disabled').addClass('btn-loading');
            ";
        }
        if (!isset($this->jsAfterVote)) {
            $this->jsAfterVote = "
                $('$selector button').prop('disabled', false).removeClass('btn-loading');
            ";
        }
        if (!isset($this->jsShowMessage)) {
            $this->jsShowMessage = "
                /** todo **/
            ";
        }
        if (!isset($this->jsErrorVote)) {
            $this->jsErrorVote = "
                /** todo **/
            ";
        }
    }
}
