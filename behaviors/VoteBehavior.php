<?php

namespace hauntd\vote\behaviors;

use hauntd\vote\models\VoteAggregate;
use hauntd\vote\traits\ModuleTrait;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;

/**
 * Class VoteBehavior
 * @package hauntd\vote\behaviors
 */
class VoteBehavior extends Behavior
{
    use ModuleTrait;

    /**
     * @var array
     */
    protected $voteAttributes;

    /**
     * @param \yii\base\Component $owner
     * @throws \yii\base\InvalidConfigException
     */
    public function attach($owner)
    {
        parent::attach($owner);
    }

    /**
     * @param $name
     * @return VoteAggregate|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getVoteAggregate($name)
    {
        $entities = $this->getModule()->entities;
        if (isset($entities[$name])) {
            return new VoteAggregate([
                'entity' => $this->getModule()->encodeEntity($name),
                'target_id' => $this->owner->getPrimaryKey(),
                'positive' => ArrayHelper::getValue($this->voteAttributes, ["{$name}Positive"]),
                'negative' => ArrayHelper::getValue($this->voteAttributes, ["{$name}Negative"]),
                'rating' => ArrayHelper::getValue($this->voteAttributes, ["{$name}Rating"]),
            ]);
        }
        return null;
    }

    /**
     * @param $name
     * @return null|integer
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserValue($name)
    {
        $entities = $this->getModule()->entities;
        if (isset($entities[$name])) {
            return ArrayHelper::getValue($this->voteAttributes, ["{$name}UserValue"]);
        }
        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $value)
    {
        if ($this->checkAttribute($name)) {
            $this->voteAttributes[$name] = !is_null($value) ? (int) $value : null;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function checkAttribute($name)
    {
        foreach (array_keys($this->getModule()->entities) as $entity) {
            if ($name == "{$entity}Positive" || $name == "{$entity}Negative" || $name == "{$entity}Rating" ||
                $name == "{$entity}UserValue") {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (isset($this->voteAttributes[$name]) || $this->checkAttribute($name)) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if ($this->checkAttribute($name)) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }
}
