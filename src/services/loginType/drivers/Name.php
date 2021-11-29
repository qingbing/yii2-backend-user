<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\services\loginType\drivers;


use YiiBackendUser\services\loginType\LoginLogic;

/**
 * 通过姓名登录
 *
 * Class Name
 * @package YiiBackendUser\services\loginType\drivers
 */
class Name extends LoginLogic
{
    /**
     * 获取登录类型
     *
     * @return string
     */
    public function getType(): string
    {
        return 'name';
    }
}