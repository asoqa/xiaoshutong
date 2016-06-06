<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use yii\log\Logger;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $access_token
 */
class User extends ActiveRecord implements IdentityInterface, RateLimitInterface
{

    const STATUS_DELETED = 0;

    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'status',
                'default',
                'value' => self::STATUS_ACTIVE
            ],
            [
                'status',
                'in',
                'range' => [
                    self::STATUS_ACTIVE,
                    self::STATUS_DELETED
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token
        ]);
        // throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username            
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token
     *            password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (! static::isPasswordResetTokenValid($token)) {
            return null;
        }
        
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token
     *            password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password
     *            password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password            
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    // 过滤掉一些字段，适用于你希望继承父类实现同时你想屏蔽掉一些敏感字段
    public function fields()
    {
        $fields = parent::fields();
        
        // 删除一些包含敏感信息的字段
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);
        
        return $fields;
    }

    /**
     * Returns the maximum number of allowed requests and the window size.
     * 
     * @param \yii\web\Request $request
     *            the current request
     * @param \yii\base\Action $action
     *            the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     *         and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        return [
            100,
            60
        ];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * 
     * @param \yii\web\Request $request
     *            the current request
     * @param \yii\base\Action $action
     *            the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     *         and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {}

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * 
     * @param \yii\web\Request $request
     *            the current request
     * @param \yii\base\Action $action
     *            the action to be executed
     * @param integer $allowance
     *            the number of allowed requests remaining.
     * @param integer $timestamp
     *            the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {}
}
