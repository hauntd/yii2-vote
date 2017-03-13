<?php

namespace hauntd\vote\models;

use Yii;
use hauntd\vote\Module;
use hauntd\vote\traits\ModuleTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\models
 */
class VoteForm extends Model
{
    use ModuleTrait;

    const ACTION_POSITIVE = 'positive';
    const ACTION_NEGATIVE = 'negative';
    const ACTION_TOGGLE = 'toggle';

    /**
     * @var string entity (e.g. "userLike" or "pageVoting")
     */
    public $entity;

    /**
     * @var integer target model id
     */
    public $targetId;

    /**
     * @var string +/-?
     */
    public $action;

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function rules()
    {
        return [
            [['entity', 'targetId', 'action'], 'required'],
            ['targetId', 'integer'],
            ['action', 'in', 'range' => [self::ACTION_NEGATIVE, self::ACTION_POSITIVE, self::ACTION_TOGGLE]],
            ['entity', 'checkModel'],
        ];
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->action == self::ACTION_NEGATIVE ? Vote::VOTE_NEGATIVE : Vote::VOTE_POSITIVE;
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkModel()
    {
        $module = $this->getModule();
        $settings = $module->getSettingsForEntity($this->entity);
        if ($settings === null) {
            $this->addError('entity', Yii::t('vote', 'This entity is not supported.'));
            return false;
        }
        $allowGuests = ArrayHelper::getValue($settings, 'allowGuests', false);
        if (Yii::$app->user->isGuest && ($settings['type'] == Module::TYPE_TOGGLE || !$allowGuests)) {
            $this->addError('entity', Yii::t('vote', 'Guests are not allowed for this voting.'));
            return false;
        }
        $targetModel = Yii::createObject($settings['modelName']);
        if ($targetModel->findOne(['id' => $this->targetId]) == null) {
            $this->addError('targetId', Yii::t('vote', 'Target model not found.'));
            return false;
        }

        return true;
    }
}
