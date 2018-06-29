<?php

namespace hauntd\vote\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vote".
 *
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\models
 * @property integer $id
 * @property integer $entity
 * @property integer $target_id
 * @property integer $user_id
 * @property string $user_ip
 * @property integer $value
 * @property integer $created_at
 *
 * @property \hauntd\vote\models\VoteAggregate $aggregate
 */
class Vote extends \yii\db\ActiveRecord
{
    const VOTE_POSITIVE = 1;
    const VOTE_NEGATIVE = 0;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%vote}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['entity', 'target_id', 'value'], 'required'],
            [['entity', 'target_id', 'user_id', 'value', 'created_at'], 'integer'],
            [['user_ip'], 'default', 'value' => function () {
                if (Yii::$app instanceof \yii\web\Application) {
                    return Yii::$app->request->userIP;
                }
                return null;
            }],
            [['user_id'], 'default', 'value' => function () {
                if (isset(Yii::$app->user) && !Yii::$app->user->isGuest) {
                    return Yii::$app->user->id;
                }
                return null;
            }],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity' => 'Entity',
            'target_id' => 'Target Model ID',
            'user_id' => 'User ID',
            'user_ip' => 'User Ip',
            'value' => 'Value',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAggregate()
    {
        return $this->hasOne(VoteAggregate::class, [
            'vote.entity' => 'vote_aggregate.entity',
            'vote.target_id' => 'vote_aggregate.target_id'
        ]);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        static::updateRating($this->attributes['entity'], $this->attributes['target_id']);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        static::updateRating($this->attributes['entity'], $this->attributes['target_id']);
        parent::afterDelete();
    }

    /**
     * @param $entity
     * @param $targetId
     */
    public static function updateRating($entity, $targetId)
    {
        $positive = static::find()->where(['entity' => $entity, 'target_id' => $targetId, 'value' => self::VOTE_POSITIVE])->count();
        $negative = static::find()->where(['entity' => $entity, 'target_id' => $targetId, 'value' => self::VOTE_NEGATIVE])->count();
        if ($positive + $negative !== 0) {
            $rating = (($positive + 1.9208) / ($positive + $negative) - 1.96 * SQRT(($positive * $negative)
                        / ($positive + $negative) + 0.9604) / ($positive + $negative)) / (1 + 3.8416 / ($positive + $negative));
        } else {
            $rating = 0;
        }
        $rating = round($rating * 10, 2);
        $aggregateModel = VoteAggregate::findOne([
            'entity' => $entity,
            'target_id' => $targetId,
        ]);
        if ($aggregateModel == null) {
            $aggregateModel = new VoteAggregate();
            $aggregateModel->entity = $entity;
            $aggregateModel->target_id = $targetId;
        }
        $aggregateModel->positive = $positive;
        $aggregateModel->negative = $negative;
        $aggregateModel->rating = $rating;
        $aggregateModel->save();
    }
}
