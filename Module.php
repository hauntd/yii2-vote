<?php

namespace hauntd\vote;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote
 */
class Module extends \yii\base\Module
{
    const TYPE_VOTING = 'voting';
    const TYPE_TOGGLE = 'toggle';

    /** @var string */
    public $controllerNamespace = 'hauntd\vote\controllers';

    /** @var array */
    public $entities;

    /** @var int */
    public $guestTimeLimit = 3600; // 1 hour per vote for guests

    /**
     * @param $entity
     * @return int
     */
    public function encodeEntity($entity)
    {
        return crc32($entity);
    }

    /**
     * @param $entity
     * @return array|null
     */
    public function getSettingsForEntity($entity)
    {
        if (!isset($this->entities[$entity])) {
            return null;
        }
        $settings = $this->entities[$entity];
        if (!is_array($settings)) {
            $settings = ['modelName' => $settings];
        }
        if (!isset($settings['type'])) {
            $settings['type'] = self::TYPE_VOTING;
        }

        return $settings;
    }
}
