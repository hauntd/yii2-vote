<?php

use hauntd\vote\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m160706_223500_vote_updates extends Migration
{
    public function up()
    {
        $this->createIndex('vote_target_user_idx', '{{%vote}}', ['entity', 'target_id', 'user_id'], false);
        $this->alterColumn('{{%vote}}', 'value', $this->boolean()->notNull());
    }

    public function down()
    {
        $this->dropIndex('vote_target_user_idx', '{{%vote}}');
        $this->alterColumn('{{%vote}}', 'value', $this->smallInteger(1)->notNull());
    }
}
