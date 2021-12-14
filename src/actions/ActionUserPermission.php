<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\actions;

use Exception;
use yii\base\Action;
use YiiPermission\logic\PermissionLogic;
use YiiHelper\helpers\AppHelper;
use YiiHelper\traits\TResponse;

/**
 * 操作 : 登录用户拥有的权限
 *
 * Class ActionUserPermission
 * @package YiiBackendUser\actions
 */
class ActionUserPermission extends Action
{
    use TResponse;

    /**
     * 获取用户权限
     *
     * @return array
     * @throws Exception
     */
    public function run()
    {
        if (AppHelper::app()->getUser()->getIsGuest()) {
            AppHelper::app()->getCache()->getOrSet(__CLASS__ . ":public:permission", function () {
                return PermissionLogic::getPublicPermission();
            }, 600);
        } else {
            $data = AppHelper::app()->getUser()->getPermissions();
        }
        return $this->success($data, '公共权限');
    }
}