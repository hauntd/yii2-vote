<?php

namespace hauntd\vote\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\Expression;
use hauntd\vote\models\VoteAggregate;
use hauntd\vote\traits\ModuleTrait;

/**
 * Class VoteQueryBehavior
 * @package hauntd\vote\behaviors
 * @property $own;er \yii\db\ActiveQuery
 */
class VoteQueryBehavior extends Behavior
{
    use ModuleTrait;

    /**
     * @param $entity
     * @return \yii\base\Component
     * @throws \yii\base\InvalidConfigException
     */
    public function withVoteAggregate($entity)
    {
        $entityEncoded = $this->getModule()->encodeEntity($entity);
        $voteAggregateTable = VoteAggregate::tableName();
        $model = new $this->owner->modelClass();
        if ((is_array($this->owner->select) && !array_search('*', $this->owner->select)) ||
            !isset($this->owner->select)) {
            $this->owner->addSelect("{$model->tableSchema->name}.*");
        }
        $this->owner
            ->leftJoin("$voteAggregateTable $entity", [
                "$entity.target_id" => new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`"),
                "$entity.entity" => $entityEncoded
            ])
            ->addSelect([
                new Expression("`$entity`.`positive` as `{$entity}Positive`"),
                new Expression("`$entity`.`negative` as `{$entity}Negative`"),
                new Expression("`$entity`.`rating` as `{$entity}Rating`"),
            ]);

        return $this->owner;
    }
}
