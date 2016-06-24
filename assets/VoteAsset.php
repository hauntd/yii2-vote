<?php

namespace hauntd\vote\assets;

use yii\web\AssetBundle;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package hauntd\vote\assets
 */
class VoteAsset extends AssetBundle
{
    public $sourcePath = '@hauntd/vote/assets/static';
    public $css = [
        'vote.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
