<?php
namespace api\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\RateLimiter;

/**
 * Site controller
 */
class ApiController extends ActiveController
{

    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                // HttpBasicAuth::className(),
                // HttpBearerAuth::className(),
                QueryParamAuth::className()
            ]
        ];
        
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;
        $behaviors['rateLimiter'] = [
            'enableRateLimitHeaders' => true,
            'class' => RateLimiter::className()
        ];
        
        return $behaviors;
    }
}
