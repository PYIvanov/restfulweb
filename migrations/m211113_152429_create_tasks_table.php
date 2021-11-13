<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tasks}}`.
 */
class m211113_152429_create_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tasks}}', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(256)->unique()->comment('Название задачи'),
            'status_id'     => $this->integer()->comment('ID статуса задачи'),
            'creator_id'    => $this->integer()->comment('ID создателя'),
            'result'        => $this->text()->comment('Результат выполнения задачи')
        ]);

        $this->createIndex(
            'idx-tasks-creator_id',
            '{{%tasks}}',
            'name'
        );

        $this->addForeignKey(
            'fk-tasks-status_id',
            '{{%tasks}}',
            'status_id',
            'status',
            'id',
            'NO ACTION',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-tasks-creator_id', '{{%tasks}}');
        $this->dropForeignKey('fk-tasks-status_id','{{%tasks}}');
        $this->dropTable('{{%tasks}}');
    }
}
