<?php
namespace api\modules\v1\controllers;

use Yii;
use api\controllers\ApiController;

class TestController extends ApiController
{
    public $modelClass = 'api\models\Test';

    public function actionIndex()
    {
        return "hello";
    }
}
