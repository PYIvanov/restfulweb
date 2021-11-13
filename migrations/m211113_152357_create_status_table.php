<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%status}}`.
 */
class m211113_152357_create_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%status}}', [
            'id'    => $this->primaryKey(),
            'name'  => $this->char(10)->comment('Статус задачи: "Новая", "В работе", "Выполнена", "Ошибка'),
        ]);

        $this->createIndex(
            'idx-status-name',
            '{{%status}}',
            'name'
        );

        $this->insert('{{%status}}', ['name' => 'Новая']);
        $this->insert('{{%status}}', ['name' => 'В работе']);
        $this->insert('{{%status}}', ['name' => 'Выполнена']);
        $this->insert('{{%status}}', ['name' => 'Ошибка']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-status-name', '{{%status}}');
        $this->dropTable('{{%status}}');
    }
}
