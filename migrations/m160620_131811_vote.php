<?php

use hauntd\vote\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m160620_131811_vote extends Migration
{
    public function up()
    {
        $this->createTable('{{%vote}}', [
            'id' => $this->primaryKey(),
            'entity' => $this->integer()->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'user_ip' => $this->string(39)->notNull()->defaultValue('127.0.0.1'),
            'value' => $this->smallInteger(1)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->createTable('{{%vote_aggregate}}', [
            'id' => $this->primaryKey(),
            'entity' => $this->integer()->unsigned()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'positive' => $this->integer()->defaultValue(0),
            'negative' => $this->integer()->defaultValue(0),
            'rating' => $this->float()->unsigned()->notNull()->defaultValue(0),
        ]);
        $this->createIndex('vote_target_idx', '{{%vote}}', ['entity', 'target_id'], false);
        $this->createIndex('vote_user_idx', '{{%vote}}', 'user_id', false);
        $this->createIndex('vote_user_ip_idx', '{{%vote}}', 'user_ip', false);
        $this->createIndex('vote_aggregate_target_idx', '{{%vote_aggregate}}', ['entity', 'target_id'], true);
    }

    public function down()
    {
        $this->dropTable('{{%vote}}');
        $this->dropTable('{{%vote_aggregate}}');
    }
}
