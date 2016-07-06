<?php

use hauntd\vote\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m160706_223457_vote_index extends Migration
{
    public function up()
    {
        $this->createIndex('vote_target_value_idx', '{{%vote}}', ['entity', 'target_id', 'value'], false);
    }

    public function down()
    {
        $this->dropIndex('vote_target_value_idx', '{{%vote}}');
    }
}
