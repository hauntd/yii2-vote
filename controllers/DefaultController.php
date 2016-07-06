<?php

namespace hauntd\vote\controllers;

use Yii;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use hauntd\vote\models\VoteAggregate;
use hauntd\vote\models\VoteForm;
use hauntd\vote\models\Vote;
use hauntd\vote\traits\ModuleTrait;
use hauntd\vote\Module;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\controllers
 */
class DefaultController extends Controller
{
    use ModuleTrait;

    public $defaultAction = 'vote';

    /**
     * @return array
     * @throws MethodNotAllowedHttpException
     */
    public function actionVote()
    {
        if (!Yii::$app->request->getIsAjax() || !Yii::$app->request->getIsPost()) {
            throw new MethodNotAllowedHttpException(Yii::t('vote', 'Forbidden method'), 405);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $module = $this->getModule();
        $form = new VoteForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $settings = $module->getSettingsForEntity($form->entity);
            if ($settings['type'] == Module::TYPE_VOTING) {
                $response = $this->processVote($form);
            } elseif ($settings['type'] == Module::TYPE_TOGGLE) {
                $response = $this->processToggle($form);
            }

            $response['aggregate'] = VoteAggregate::findOne([
                'entity' => $module->encodeEntity($form->entity),
                'target_id' => $form->targetId
            ]);
        } else {
            $response = ['success' => false, 'errors' => $form->errors];
        }

        return $response;
    }

    /**
     * Processes a vote (+/-) request.
     *
     * @param VoteForm $form
     * @return array
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function processVote(VoteForm $form)
    {
        /* @var $vote Vote */
        $module = $this->getModule();
        $response = ['success' => false];
        $searchParams = ['entity' => $module->encodeEntity($form->entity), 'target_id' => $form->targetId];

        if (Yii::$app->user->isGuest) {
            $vote = Vote::find()
                ->where($searchParams)
                ->andWhere(['user_ip' => Yii::$app->request->userIP])
                ->andWhere('UNIX_TIMESTAMP() - created_at < :limit', [':limit' => $module->guestTimeLimit])
                ->one();
        } else {
            $vote = Vote::findOne(array_merge($searchParams, ['user_id' => Yii::$app->user->id]));
        }

        if ($vote == null) {
            $response = $this->createVote($module->encodeEntity($form->entity), $form->targetId, $form->getValue());
        } else {
            if ($vote->value !== $form->getValue()) {
                $vote->value = $form->getValue();
                if ($vote->save()) {
                    $response = ['success' => true, 'changed' => true];
                }
            }
        }

        return $response;
    }

    /**
     * Processes a vote toggle request (like/favorite etc).
     *
     * @param VoteForm $form
     * @return array
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function processToggle(VoteForm $form)
    {
        /* @var $vote Vote */
        $module = $this->getModule();
        $vote = Vote::findOne([
            'entity' => $module->encodeEntity($form->entity),
            'target_id' => $form->targetId,
            'user_id' => Yii::$app->user->id
        ]);

        if ($vote == null) {
            $response = $this->createVote($module->encodeEntity($form->entity), $form->targetId, $form->getValue());
        } else {
            $vote->delete();
            $response = ['success' => true];
        }

        return $response;
    }

    /**
     * Creates new vote entry and returns response data.
     *
     * @param $entity
     * @param $targetId
     * @param $value
     * @return array
     */
    private function createVote($entity, $targetId, $value)
    {
        $vote = new Vote();
        $vote->entity = $entity;
        $vote->target_id = $targetId;
        $vote->value = $value;

        if ($vote->save()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => $vote->errors];
        }
    }
}
