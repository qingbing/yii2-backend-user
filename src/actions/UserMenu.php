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
use YiiHelper\helpers\Req;
use YiiHelper\traits\TResponse;
use YiiHelper\traits\TValidator;
use YiiPermission\models\PermissionMenu;
use YiiPermission\models\PermissionUserRole;
use Zf\Helper\Business\ObjectTree;
use Zf\Helper\Exceptions\CustomException;

/**
 * 操作 : 获取用户菜单
 *
 * Class UserMenu
 * @package YiiBackendUser\actions
 */
class UserMenu extends Action
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
     * @throws Exception
     */
    protected function beforeRun()
    {
        // 参数验证和获取
        $this->params = $this->validateParams([
            ['uid', 'exist', 'label' => '用户', 'default' => Req::getUid(), 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['isTree', 'boolean', 'label' => '是否结构化', 'default' => true],
            ['type', 'in', 'range' => array_keys(PermissionMenu::types()), 'label' => '菜单类型', 'default' => PermissionMenu::TYPE_MENU],
        ]);
        $this->user   = User::findOne([
            'uid' => $this->params['uid'],
        ]);
        if (null === $this->user) {
            throw new CustomException('用户不存在');
        }
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
        // 查询当前用户能够访问的所有菜单ID
        if (!AppHelper::app()->getUser()->getIsSuper()) {
            $menus   = PermissionMenu::find()
                ->alias('menu')
                ->select('menu.id')
                ->andWhere(['=', 'menu.is_enable', IS_YES])
                ->andWhere(['=', 'menu.type', $this->params['type']])
                ->asArray()
                ->all();
            $menuIds = array_column($menus, 'id');
        } else {
            // 获取用户菜单ID
            $userMenus = PermissionMenu::find()
                ->select('menu.id')
                ->alias('menu')
                ->andWhere(['=', 'menu.is_enable', IS_YES])
                ->andWhere(['=', 'menu.type', $this->params['type']])
                ->joinWith(['roles'])
                ->leftJoin(PermissionUserRole::tableName() . ' AS userRole', 'userRole.role_code=role.code')
                ->andWhere(['=', 'userRole.uid', $this->user->uid])
                ->asArray()
                ->all();
            // 获取公共菜单ID
            $publicMenus = PermissionMenu::find()
                ->alias('menu')
                ->select('menu.id')
                ->andWhere(['=', 'menu.is_enable', IS_YES])
                ->andWhere(['=', 'menu.type', $this->params['type']])
                ->andWhere(['=', 'menu.is_public', IS_YES])
                ->asArray()
                ->all();
            $menuIds     = array_unique(array_column(array_merge($userMenus, $publicMenus), 'id'));
        }
        $menus = PermissionMenu::find()
            ->alias('menu')
            ->orderBy('menu.sort_order DESC, api.sort_order DESC')
            ->joinWith(['apis'])
            ->andWhere(['in', 'menu.id', $menuIds])
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
            ->setData($treeData)
            ->getTreeData();
        return $this->success(array_values($treeData));
    }
}