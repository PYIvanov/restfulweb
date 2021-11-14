<?php

namespace app\controllers;

use app\models\Tasks;
use PHPUnit\Util\Log\JSON;
use yii\base\Event;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

class TasksController extends ActiveController
{
    public $modelClass = 'app\models\Tasks';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['create', 'update', 'delete', 'view', 'close'],
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'view', 'close'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'close'  => ['POST']
            ],
        ];

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // проверяем может ли пользователь редактировать или удалить запись
        // выбрасываем исключение ForbiddenHttpException если доступ запрещен
        if ($action === 'delete') {
            if (($model->creator_id !== (int)\Yii::$app->user->id) || ($model->status_id !== 1)) {
                throw new ForbiddenHttpException("Вы можете удалять только собственные задачи в статусе `Новая`!");
            }
        } elseif ($action === 'update') {
            if ($model->creator_id !== (int)\Yii::$app->user->id)
                throw new ForbiddenHttpException(sprintf('Вы можете редактировать %s только собственные задачи!', $action));
        }
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        Event::on(Tasks::class, Tasks::EVENT_AFTER_FIND, function ($event) {
            $event->sender->scenario = Tasks::SCENARIO_SEARCH_MULTIPLE;
        });

        if(!empty(\Yii::$app->request->post('status'))) {
            $query = Tasks::find()
                ->joinWith('status')
                ->where(['status.name' => \Yii::$app->request->post('status')]);
        } else {
            $query = Tasks::find();
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 3,
            ],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function actionView()
    {
        Event::on(Tasks::class, Tasks::EVENT_AFTER_FIND, function ($event) {
            $event->sender->scenario = Tasks::SCENARIO_SEARCH_ONE;
        });

        $task = Tasks::findOne(\Yii::$app->request->get('id'));
        if ($task !== null) {
            $task->status_id = '2';
            if (!$task->save()) {
                throw new \Exception (implode("<br />" , ArrayHelper::getColumn($task->errors , 0 , false)));
            }
        }

        return $task;
    }

    /**
     * @throws \Exception
     */
    public function actionClose()
    {
        Event::on(Tasks::class, Tasks::EVENT_BEFORE_VALIDATE, function ($event) {
            $event->sender->scenario = Tasks::SCENARIO_CLOSE;
        });

        $task = Tasks::findOne(\Yii::$app->request->post('id'));
        $message = [];
        if ($task !== null) {
            if (strpos(\Yii::$app->request->post('result'), 'ERROR: ') === false)
            {
                $task->status_id = '3'; // Set status code 'Выполнена'
                $task->result = \Yii::$app->request->post('result');
                $message = [
                    'message' => 'Статус задачи изменён на `Выполнена`',
                    'status_id' => $task->status_id,
                    'result' => \Yii::$app->request->post('result')
                ];
            } else {
                $task->status_id = '4'; // Set status code 'Ошибка'
                $task->result = \Yii::$app->request->post('result');
                $message = [
                    'message' => 'Статус задачи изменён на `Ошибка`',
                    'status_id' => $task->status_id,
                    'result' => \Yii::$app->request->post('result')
                ];
            }

            if (!$task->save()) {
                throw new \Exception (implode("<br />", ArrayHelper::getColumn($task->errors, 0, false)));
            }
        }
        return $message;
    }
}