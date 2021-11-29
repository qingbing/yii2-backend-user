<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\validators;


use Exception;
use Yii;
use yii\validators\Validator;
use YiiBackendUser\models\User;
use Zf\Helper\Exceptions\ForbiddenHttpException;

/**
 * 用户登录密码验证
 *
 * Class UserPasswordValidator
 * @package YiiBackendUser\validators
 */
class UserPasswordValidator extends Validator
{
    /**
     * @var int 需要验证的用户ID，默认为登录用户
     */
    public $uid;
    /**
     * @var string 用户模型中的用户id字段名
     */
    public $uidField = 'uid';
    /**
     * @var string 用户模型中的密码字段名
     */
    public $passwordField;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (null === $this->message) {
            $this->message = Yii::t('yii', '{attribute}不正确');
        }
    }

    /**
     * 获取需要验证的用户模型
     *
     * @return \yii\web\IdentityInterface|null|User
     * @throws \Throwable
     * @throws Exception
     */
    protected function getUser()
    {
        if (Yii::$app->getUser()->getIsGuest()) {
            throw new ForbiddenHttpException("您无权操作，请先登录");
        }
        $user = Yii::$app->getUser()->getIdentity();
        if (null === $this->uid) {
            return $user;
        }
        $userModelClass = Yii::createObject(get_class($user));
        return $userModelClass::findOne([
            $this->uidField => $this->uid,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     * @return array|null|void
     * @throws \Throwable
     */
    public function validateValue($value)
    {
        $user = $this->getUser();
        if (!$user->validatePassword($value)) {
            return [$this->message, []];
        }
    }
}