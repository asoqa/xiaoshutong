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

    public function actionView($id)
    {
        return User::findOne($id);
    }

    public function actionIndex()
    {
        return "aa";
    }
}
