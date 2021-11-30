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
 * Class UserPermission
 * @package YiiBackendUser\actions
 */
class UserPermission extends Action
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
        $data = [
            [
                'route'      => 'configure',
                'icon'       => 'el-icon-menu',
                'label'      => '配置管理',
                'linkRoutes' => [],
                'subItems'   => [
                    [
                        'route'      => '/table-header',
                        'icon'       => 'el-icon-menu',
                        'label'      => '表头管理',
                        'linkRoutes' => [],
                    ],
                    [
                        'route'      => '/form',
                        'icon'       => 'el-icon-menu',
                        'label'      => '表单管理',
                        'linkRoutes' => [],
                    ],
                    [
                        'route'      => 'interface-manage',
                        'icon'       => 'el-icon-menu',
                        'label'      => '接口管理',
                        'linkRoutes' => [],
                        'subItems'   => [
                            [
                                'route'      => '/interface-manage/system',
                                'icon'       => 'el-icon-menu',
                                'label'      => '接口系统',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/interface-manage/type',
                                'icon'       => 'el-icon-menu',
                                'label'      => '接口分类',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/interface-manage/list',
                                'icon'       => 'el-icon-menu',
                                'label'      => '接口管理',
                                'linkRoutes' => [],
                            ],
                        ],
                    ],
                    [
                        'route'      => '/replace',
                        'icon'       => 'el-icon-menu',
                        'label'      => '替换模版',
                        'linkRoutes' => [],
                    ],
                    [
                        'route'      => 'logs',
                        'icon'       => 'el-icon-menu',
                        'label'      => '日志管理',
                        'linkRoutes' => [],
                        'subItems'   => [
                            [
                                'route'      => '/logs/operate',
                                'icon'       => 'el-icon-menu',
                                'label'      => '操作提示日志',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/logs/interface-route',
                                'icon'       => 'el-icon-menu',
                                'label'      => '接口路由日志',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/logs/interface-access',
                                'icon'       => 'el-icon-menu',
                                'label'      => '接口访问日志',
                                'linkRoutes' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'route'      => '/index',
                'icon'       => 'el-icon-menu',
                'label'      => '首页',
                'linkRoutes' => [
                    '/test1' => '测试1',
                    '/test2' => '测试2',
                    '/test3' => '测试3',
                ],
                'subItems'   => [
                    [
                        'route'      => 'member',
                        'icon'       => 'el-icon-menu',
                        'label'      => '模板示例',
                        'linkRoutes' => [],
                        'subItems'   => [
                            [
                                'route'      => '/form/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '表单项',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/form/view',
                                'icon'       => 'el-icon-menu',
                                'label'      => '表单显示',
                                'linkRoutes' => [],
                            ],
                        ],
                    ],
                    [
                        'route'      => 'member',
                        'icon'       => 'el-icon-menu',
                        'label'      => '人员管理',
                        'linkRoutes' => [],
                        'subItems'   => [
                            [
                                'route'      => '/programmer/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '程序员管理',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/admin/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '管理员管理',
                                'linkRoutes' => [],
                            ],
                        ],
                    ],
                    [
                        'route'      => '/settings',
                        'icon'       => 'el-icon-menu',
                        'label'      => '网站设置',
                        'linkRoutes' => [],
                        'subItems'   => [
                            [
                                'route'      => '/nav/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '导航管理',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/table-header/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '表头设置',
                                'linkRoutes' =>
                                    [
                                        '/table-header/add'  => '添加表头',
                                        '/table-header/edit' => '编辑表头',
                                        '/table-header/view' => '查看表头',
                                    ],
                            ],
                            [
                                'route'      => '/form-settings/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '表单配置',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/tempalte/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '替换模板',
                                'linkRoutes' => [],
                            ],
                            [
                                'route'      => '/protocol/index',
                                'icon'       => 'el-icon-menu',
                                'label'      => '协议管理',
                                'linkRoutes' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'route'      => '/support',
                'icon'       => 'el-icon-menu',
                'label'      => '关于我们',
                'linkRoutes' => [],
                'subItems'   => [
                    [
                        'route'      => '/about-us',
                        'icon'       => 'el-icon-menu',
                        'label'      => '关于我们',
                        'linkRoutes' => [],
                    ],
                    [
                        'route'      => '/contact-us',
                        'icon'       => 'el-icon-menu',
                        'label'      => '联系我们',
                        'linkRoutes' => [],
                    ],
                ],
            ],
        ];
        /*
        if (AppHelper::app()->getUser()->getIsGuest()) {
            AppHelper::app()->getCache()->getOrSet(__CLASS__ . ":public:permission", function () {
                return PermissionLogic::getPublicPermission();
            }, 600);
        } else {
            $data = AppHelper::app()->getUser()->getPermissions();
        }
        */
        return $this->success($data, '公共权限');
    }
}