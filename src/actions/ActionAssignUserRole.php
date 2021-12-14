<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\actions;

use Exception;
use yii\base\Action;
use YiiBackendUser\models\User;
use YiiHelper\helpers\AppHelper;
use YiiHelper\traits\TLoginRequired;
use YiiHelper\traits\TResponse;
use YiiHelper\traits\TValidator;
use YiiPermission\logic\PermissionLogic;
use Zf\Helper\Traits\Models\TLabelYesNo;

/**
 * 操作 : 为用户分配角色
 *
 * Class ActionAssignUserRole
 * @package YiiBackendUser\actions
 */
class ActionAssignUserRole extends Action
{
    use TValidator;
    use TLoginRequired;
    use TResponse;

    /**
     * @var array 传递参数
     */
    protected $params = [];

    /**
     * 执行前的参数检查
     *
     * @return bool
     * @throws Exception
     */
    protected function beforeRun()
    {
        // 确保登录
        self::loginRequired();
        // 获取用户的权限
        $hasRoles = AppHelper::app()->getUser()->getPermissions()['roles'];
        // 参数验证和获取
        $this->params = $this->validateParams([
            [['uid', 'is_enable', 'role_codes'], 'required'],
            ['is_enable', 'in', 'label' => '是否有效', 'range' => array_keys(TLabelYesNo::isLabels())],
            ['uid', 'exist', 'label' => '用户', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            [
                'role_codes',
                'each',
                'label' => '角色',
                'rule'  => [
                    'in', 'range' => array_keys($hasRoles), 'message' => '不能操作的{attribute}({value})'
                ]
            ]
        ], null, false, ['role_codes'], ',');
        return true;
    }

    /**
     * 给用户分配角色
     *
     * @return array
     * @throws \Throwable
     * @throws Exception
     */
    public function run()
    {
        $status = PermissionLogic::assignUserRole($this->params['uid'], $this->params['role_codes'], $this->params['is_enable']);
        return $this->success($status, '分配角色成功');
    }
}