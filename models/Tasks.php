<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string|null $name Название задачи
 * @property int|null $status_id ID статуса задачи
 * @property int|null $creator_id ID создателя
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {

        return [
            ['name', 'required'],
            ['status_id', 'default', 'value' => '1'],
            ['creator_id', 'default', 'value' => Yii::$app->user->id],
            [['status_id', 'creator_id'], 'integer'],
            [['name'], 'string', 'max' => 256],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название задачи',
            'status_id' => 'ID статуса задачи',
            'creator_id' => 'ID создателя',
        ];
    }

    public function fields()
    {
        return [
            'id'
        ];
    }
}
