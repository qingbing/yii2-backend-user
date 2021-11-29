<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\interfaces;


use YiiHelper\services\interfaces\ICurdService;

/**
 * 接口 ： 成员管理
 *
 * Interface IMemberService
 * @package YiiBackendUser\interfaces
 */
interface IMemberService extends ICurdService
{
    /**
     * 重置用户密码
     *
     * @param array $params
     * @return bool
     */
    public function resetPassword(array $params = []): bool;

    /**
     * 查看用户所有账户信息
     *
     * @param array|null $params
     * @return array
     */
    public function accounts(array $params = []): array;

    /**
     * 启用或禁用成员
     *
     * @param array $params
     * @return bool
     */
    public function changeStatus(array $params = []): bool;

    /**
     * 设置用户是否超级管理员
     *
     * @param array|null $params
     * @return bool
     */
    public function changeSuperStatus(array $params = []): bool;
}