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
 * @property string|null $result Результат выполнения задачи
 */
class Tasks extends \yii\db\ActiveRecord
{
    const SCENARIO_SEARCH_ONE = 'scenario_search';
    const SCENARIO_SEARCH_MULTIPLE = 'scenario_search_multiple';
    const SCENARIO_CLOSE = 'scenario_close';

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
            ['id', 'integer', 'on'                      => self::SCENARIO_SEARCH_ONE],
            [['id', 'result'], 'required', 'on'         => self::SCENARIO_CLOSE],
            ['id', 'integer', 'on'                      => self::SCENARIO_CLOSE],
            ['name', 'required'],
            ['status_id', 'default', 'value' => '1'],
            ['creator_id', 'default', 'value' => Yii::$app->user->id],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['name'], 'string', 'max' => 256],
            [['name'], 'unique'],
            [['result'], 'string'],
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
            'result' => 'Результат выполнения задачи',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['scenario_search_one'] = ['id'];
        $scenarios['scenario_search_multiple'] = ['status'];
        $scenarios['scenario_close'] = ['id', 'result'];
        return $scenarios;
    }

    public function fields()
    {
        $fields = parent::fields();

        switch ($this->scenario) {
            case self::SCENARIO_SEARCH_MULTIPLE:
            case self::SCENARIO_SEARCH_ONE:
                break;
            default:
                unset($fields['name'], $fields['status_id'], $fields['creator_id'], $fields['result']);
        }

        return $fields;
    }
}
