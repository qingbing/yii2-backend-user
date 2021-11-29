<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\services;


use Exception;
use YiiBackendUser\interfaces\IPersonalService;
use YiiBackendUser\models\User;
use YiiBackendUser\models\UserAccount;
use YiiHelper\abstracts\Service;
use YiiHelper\helpers\AppHelper;
use Zf\Helper\Exceptions\BusinessException;
use Zf\Helper\Exceptions\ForbiddenHttpException;

/**
 * 服务 ： 个人信息管理
 *
 * Class PersonalService
 * @package YiiBackendUser\services
 */
class PersonalService extends Service implements IPersonalService
{
    /**
     * 个人信息
     *
     * @return mixed|\yii\web\IdentityInterface|User
     * @throws Exception
     * @throws \Throwable
     */
    public function info()
    {
        return $this->getUser();
    }

    /**
     * 修改个人信息
     *
     * @param array $params
     * @return bool
     * @throws \Throwable
     * @throws Exception
     */
    public function changeInfo(array $params): bool
    {
        $model = $this->getUser();
        $model->setFilterAttributes($params);
        return $model->saveOrException();
    }

    /**
     * 修改个人密码
     *
     * @param array $params
     * @return bool
     * @throws \Throwable
     * @throws Exception
     */
    public function resetPassword(array $params): bool
    {
        $user = $this->getUser();
        if ($user->validatePassword($params['oldPassword'])) {
            $user->password = $user->generatePassword($params['newPassword']);
            return $user->saveOrException();
        }
        throw new BusinessException("原始密码输入不正确");
    }

    /**
     * 获取登录用户模型
     *
     * @return \yii\web\IdentityInterface|User
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    protected function getUser()
    {
        if (\Yii::$app->getUser()->getIsGuest()) {
            throw new ForbiddenHttpException("请先登录，您无权访问该功能");
        }
        return \Yii::$app->getUser()->getIdentity();
    }

    /**
     * 个人账户信息
     *
     * @return mixed|\yii\web\IdentityInterface|User
     * @throws Exception
     * @throws \Throwable
     */
    public function accounts()
    {
        return $this->getUser()->accounts;
    }

    /**
     * 添加账户信息
     *
     * @param array $params
     * @return bool
     * @throws \Throwable
     * @throws Exception
     */
    public function addAccount(array $params): bool
    {
        $model = new UserAccount();
        $model->setFilterAttributes($params);
        $model->uid = $this->getUser()->uid;
        $model->setFilterAttributes($params);
        return $model->saveOrException();
    }

    /**
     * 编辑账户信息
     *
     * @param array $params
     * @return bool
     * @throws BusinessException
     */
    public function editAccount(array $params): bool
    {
        throw new BusinessException("该功能尚未开通");
        // $model = $this->getUserAccount($params);
        // unset($params['id']);
        // $model->setFilterAttributes($params);
        // return $model->saveOrException();
    }

    /**
     * 修改账户状态（启用|禁用）
     *
     * @param array $params
     * @return bool
     * @throws \Throwable
     * @throws Exception
     */
    public function changeAccountStatus(array $params): bool
    {
        $model        = $this->getUserAccount($params);
        $loginAccount = AppHelper::app()->getUser()->getUserAccount();
        if ($loginAccount->id === $model->id) {
            throw new BusinessException('不能修改当前登录账户状态');
        }
        if ($model->is_enable == $params['status']) {
            return true;
        }
        $model->is_enable = $params['status'];
        return $model->saveOrException();
    }

    /**
     * 删除账户信息
     *
     * @param array $params
     * @return bool
     * @throws BusinessException
     */
    public function delAccount(array $params): bool
    {
        throw new BusinessException("该功能尚未开通");
        // $model = $this->getUserAccount($params);
        // return $model->delete();
    }

    /**
     * 获取个人账户模型
     *
     * @param array $params
     * @return UserAccount|null
     * @throws \Throwable
     * @throws Exception
     */
    protected function getUserAccount(array $params)
    {
        $user  = $this->getUser();
        $model = UserAccount::findOne([
            "uid" => $user->uid,
            "id"  => $params['id'],
        ]);
        if (null === $model) {
            throw new BusinessException("个人账户不存在");
        }
        return $model;
    }
}