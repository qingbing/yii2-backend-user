<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\interfaces;

use YiiHelper\services\interfaces\IService;

/**
 * 接口 ： 个人信息管理
 *
 * Interface IPersonalService
 * @package YiiBackendUser\interfaces
 */
interface IPersonalService extends IService
{
    /**
     * 个人信息
     *
     * @return mixed
     */
    public function info();

    /**
     * 修改个人信息
     *
     * @param array $params
     * @return bool
     */
    public function changeInfo(array $params): bool;

    /**
     * 修改个人密码
     *
     * @param array $params
     * @return bool
     */
    public function resetPassword(array $params): bool;

    /**
     * 个人账户信息
     *
     * @return mixed
     */
    public function accounts();

    /**
     * 添加账户信息
     *
     * @param array $params
     * @return bool
     */
    public function addAccount(array $params): bool;

    /**
     * 编辑账户信息
     *
     * @param array $params
     * @return bool
     */
    public function editAccount(array $params): bool;

    /**
     * 修改账户状态（启用|禁用）
     *
     * @param array $params
     * @return bool
     */
    public function changeAccountStatus(array $params): bool;

    /**
     * 删除账户信息
     *
     * @param array $params
     * @return bool
     */
    public function delAccount(array $params): bool;
}