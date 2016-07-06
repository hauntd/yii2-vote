<?php

namespace hauntd\vote\behaviors;

use hauntd\vote\models\Vote;
use hauntd\vote\models\VoteAggregate;
use hauntd\vote\traits\ModuleTrait;
use Yii;
use yii\base\Behavior;
use yii\db\Expression;

/**
 * Class VoteQueryBehavior
 * @package hauntd\vote\behaviors
 * @property $own;er \yii\db\ActiveQuery
 */
class VoteQueryBehavior extends Behavior
{
    use ModuleTrait;

    /**
     * @var bool
     */
    protected $selectAdded = false;

    /**
     * Include vote aggregate model/values.
     *
     * @param $entity
     * @return \yii\base\Component
     * @throws \yii\base\InvalidConfigException
     */
    public function withVoteAggregate($entity)
    {
        $entityEncoded = $this->getModule()->encodeEntity($entity);
        $voteAggregateTable = VoteAggregate::tableName();
        $model = new $this->owner->modelClass();
        $this->initSelect($model);

        $this->owner
            ->leftJoin("$voteAggregateTable {$entity}Aggregate", [
                "{$entity}Aggregate.target_id" => new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`"),
                "{$entity}Aggregate.entity" => $entityEncoded
            ])
            ->addSelect([
                new Expression("`{$entity}Aggregate`.`positive` as `{$entity}Positive`"),
                new Expression("`{$entity}Aggregate`.`negative` as `{$entity}Negative`"),
                new Expression("`{$entity}Aggregate`.`rating` as `{$entity}Rating`"),
            ]);

        return $this->owner;
    }

    /**
     * Include user vote status.
     *
     * @param $entity
     * @return \yii\base\Component
     * @throws \yii\base\InvalidConfigException
     */
    public function withUserVote($entity)
    {
        $entityEncoded = $this->getModule()->encodeEntity($entity);
        $model = new $this->owner->modelClass();
        $voteTable = Vote::tableName();
        $this->initSelect($model);

        $joinCondition = [
            "$entity.target_id" => new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`"),
            "$entity.entity" => $entityEncoded,
        ];

        if (Yii::$app->user->isGuest) {
            $joinCondition["{$entity}.user_ip"] = Yii::$app->request->userIP;
            $joinCondition["{$entity}.user_id"] = null;
            $this->owner->addGroupBy("`{$model->tableSchema->name}`.`{$model->tableSchema->primaryKey[0]}`");
        } else {
            $joinCondition["{$entity}.user_id"] = Yii::$app->user->id;
        }

        $this->owner
            ->leftJoin("$voteTable $entity", $joinCondition)
            ->addSelect([
                new Expression("`$entity`.`value` as `{$entity}UserValue`")]);

        return $this->owner;
    }

    /**
     * Add `{{%table}}`.* as first table attributes to select.
     *
     * @param $model
     */
    protected function initSelect($model)
    {
        if (!$this->selectAdded && (is_array($this->owner->select) && !array_search('*', $this->owner->select)) ||
            !isset($this->owner->select)) {
            $this->owner->addSelect("{$model->tableSchema->name}.*");
            $this->selectAdded = true;
        }
    }
}
