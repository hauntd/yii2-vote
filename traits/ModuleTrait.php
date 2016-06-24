<?php

namespace hauntd\vote\traits;

use Yii;
use hauntd\vote\Module;
use yii\base\InvalidConfigException;

/**
 * Trait ModuleTrait
 * @package hauntd\vote\traits
 */
trait ModuleTrait
{
    /**
     * @return \hauntd\vote\Module
     * @throws InvalidConfigException
     */
    public function getModule()
    {
        if (Yii::$app->hasModule('vote') && ($module = Yii::$app->getModule('vote')) instanceof Module) {
            return $module;
        }

        throw new InvalidConfigException('Module "vote" is not set.');
    }
}
