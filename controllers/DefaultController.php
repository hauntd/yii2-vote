<?php

namespace hauntd\vote\controllers;

use hauntd\vote\actions\VoteAction;
use Yii;
use yii\web\Controller;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\controllers
 */
class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'vote';

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'vote' => [
                'class' => VoteAction::className(),
            ]
        ];
    }
}
