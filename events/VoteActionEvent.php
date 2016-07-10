<?php

namespace hauntd\vote\events;

use hauntd\vote\models\VoteForm;
use yii\base\Event;

/**
 * Class VoteActionEvent
 * @package hauntd\vote\events
 */
class VoteActionEvent extends Event
{
    /**
     * @var VoteForm
     */
    public $voteForm;

    /**
     * @var array
     */
    public $responseData;
}
