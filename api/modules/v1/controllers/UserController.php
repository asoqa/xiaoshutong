<?php
namespace api\modules\v1\controllers;

use Yii;
use common\models\User;
use api\controllers\ApiController;

/**
 * Site controller
 */
class UserController extends ApiController
{

    public $modelClass = 'common\models\User';

    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    public function actionView($id)
    {
        return User::findOne($id);
    }

    public function actionIndex()
    {
        return "aa";
    }
}
