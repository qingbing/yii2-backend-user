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
use YiiHelper\traits\TResponse;
use YiiHelper\traits\TValidator;
use YiiPermission\models\PermissionMenu;
use Zf\Helper\Business\ObjectTree;

/**
 * 操作 : 获取用户菜单
 *
 * Class ActionUserMenu
 * @package YiiBackendUser\actions
 */
class ActionUserMenu extends Action
{
    use TResponse;
    use TValidator;
    /**
     * @var array 请求参数
     */
    protected $params = [];
    /**
     * @var User 获取菜单的用户模型
     */
    protected $user;

    /**
     * 执行前的参数检查
     *
     * @return bool
     */
    /**
     * @return bool
     * @throws \Throwable
     * @throws Exception
     */
    protected function beforeRun()
    {
        // 参数验证和获取
        $this->params = $this->validateParams([
            ['uid', 'exist', 'label' => '用户', 'default' => '', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['isTree', 'boolean', 'label' => '是否结构化', 'default' => true],
            ['type', 'in', 'range' => array_keys(PermissionMenu::treeMap()), 'label' => '菜单类型', 'default' => PermissionMenu::TYPE_MENU],
        ]);
        $this->user   = User::findOne([
            'uid' => $this->params['uid'],
        ]);
        return true;
    }

    /**
     * 获取用户菜单，主要为前端准备的树形结构
     *
     * @return array
     * @throws Exception
     */
    public function run()
    {
        if ($this->user) {
            $menus = $this->user->getMenus($this->params['type'], false, DIRECTION_BOTH);
        } else {
            $menus = array_filter(AppHelper::app()->getUser()->getPermissions()['menus'], function ($var) {
                return $this->params['type'] == $var['type'];
            });
        }
        $menuCodes = array_keys($menus);
        $menus     = PermissionMenu::find()
            ->alias('menu')
            ->orderBy('menu.sort_order DESC, api.sort_order DESC')
            ->joinWith(['apis'])
            ->andWhere(['in', 'menu.code', $menuCodes])
            ->andWhere('api.is_enable=:is_enable OR api.is_enable IS NULL', [
                ':is_enable' => IS_YES
            ])
            ->asArray()
            ->all();
        // 过滤数据
        $treeData = [];
        foreach ($menus as $menu) {
            $treeData[] = [
                'code'       => $menu['code'],
                'parentCode' => $menu['parent_code'],
                'route'      => $menu['path'],
                'icon'       => $menu['icon'],
                'label'      => $menu['name'],
                'linkRoutes' => array_column($menu['apis'], 'path'),
            ];
        }
        $treeData = ObjectTree::getInstance()
            ->setId('code')
            ->setPid('parentCode')
            ->setTopTag('')
            ->setSubDataName('subItems')
            ->setSourceData($treeData)
            ->getTreeData();
        return $this->success(array_values($treeData));
    }
}