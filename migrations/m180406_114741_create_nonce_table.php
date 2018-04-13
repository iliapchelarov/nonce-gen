<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `nonce`.
 */
class m180406_114741_create_nonce_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('nonce', [
            'id' => $this->primaryKey(),
            'nonce' => $this->integer(10)->notNull()->unique(),
            'key' => $this->string(256)->notNull(),
            'expires' => $this->integer(10)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('nonce');
    }
}
