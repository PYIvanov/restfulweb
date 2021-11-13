<?php

namespace app\controllers;

use yii\filters\ContentNegotiator;
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
            'class' => \yii\filters\AccessControl::className(),
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
            'class' => \yii\filters\VerbFilter::class,
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
                throw new ForbiddenHttpException((int)($model->status_id !== '1')
                    ."{$model->creator_id} {$model->status_id} Вы можете удалять только собственные задачи!"
                    .\Yii::$app->user->id);
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
        return $actions;
    }

    public function actionView()
    {
        return \Yii::$app->request->get();
    }

    public function actionClose()
    {
        return [];
    }
}