<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\controllers;


use Exception;
use YiiBackendUser\actions\AssignUserRole;
use YiiBackendUser\interfaces\IMemberService;
use YiiBackendUser\models\User;
use YiiBackendUser\models\UserAccount;
use YiiBackendUser\services\MemberService;
use YiiBackendUser\validators\UserPasswordValidator;
use YiiHelper\abstracts\RestController;
use Zf\Helper\Traits\Models\TLabelEnable;
use Zf\Helper\Traits\Models\TLabelSex;
use Zf\Helper\Traits\Models\TLabelYesNo;

/**
 * 控制器 ： 成员管理
 *
 * Class MemberController
 * @package YiiBackendUser\controllers
 *
 * @property-read IMemberService $service
 */
class MemberController extends RestController
{
    public $serviceInterface = IMemberService::class;
    public $serviceClass     = MemberService::class;

    public function actions()
    {
        return [
            // 给用户分配角色
            'assign-role' => AssignUserRole::class,
        ];
    }

    /**
     * 成员列表
     *
     * @return array
     * @throws Exception
     */
    public function actionList()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            ['uid', 'integer', 'label' => 'UID'],
            ['nickname', 'string', 'label' => '用户昵称'],
            ['real_name', 'string', 'label' => '姓名'],
            ['sex', 'in', 'label' => '性别', 'range' => array_keys(TLabelSex::sexLabels())],

            ['email', 'string', 'label' => '邮箱账户'],
            ['mobile', 'string', 'label' => '手机号码'],
            ['qq', 'string', 'label' => 'QQ'],
            ['id_card', 'string', 'label' => '身份证号'],
            ['is_enable', 'in', 'label' => '启用状态', 'range' => array_keys(TLabelEnable::enableLabels())],
            ['is_super', 'in', 'label' => '是否超管', 'range' => array_keys(TLabelYesNo::isLabels())],
            ['refer_uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['is_valid', 'in', 'label' => '是否有效', 'range' => array_keys(TLabelYesNo::isLabels())],

            ['time_type', 'in', 'label' => '时间', 'range' => array_keys(User::timeTypes())],
            ['start_at', 'datetime', 'label' => '注册开始时间', 'format' => 'php:Y-m-d H:i:s'],
            ['end_at', 'datetime', 'label' => '注册结束时间', 'format' => 'php:Y-m-d H:i:s'],
        ], null, true);
        // 业务处理
        $res = $this->service->list($params);
        // 渲染结果
        return $this->success($res, '系统列表');
    }

    /**
     * 添加成员
     *
     * @return array
     * @throws Exception
     */
    public function actionAdd()
    {
        // 参数验证和获取
        $accountType = UserAccount::getDefaultAccountType();
        $rules       = [
            [['nickname', 'account', 'password', 'sex', 'is_enable'], 'required'],
            ['nickname', 'unique', 'label' => '用户昵称', 'targetClass' => User::class, 'targetAttribute' => 'nickname'],
            ['account', 'unique', 'label' => '默认账号', 'targetClass' => UserAccount::class, 'targetAttribute' => 'account', 'filter' => ['=', 'type', $accountType]],
            ['password', 'string', 'label' => '登录密码', 'min' => 6],
            ['sex', 'in', 'label' => '性别', 'default' => 0, 'range' => array_keys(TLabelSex::sexLabels())],
            ['is_enable', 'in', 'label' => '启用状态', 'range' => array_keys(TLabelEnable::enableLabels())],
            ['expire_ip', 'string', 'label' => '有效IP地址'],
            ['expire_begin_at', 'date', 'label' => '生效日期', 'format' => 'php:Y-m-d'],
            ['expire_end_at', 'date', 'label' => '失效日期', 'format' => 'php:Y-m-d'],
        ];
        array_push($rules, UserAccount::getAccountValidatorRule($accountType));
        $params = $this->validateParams($rules);
        // 业务处理
        $res = $this->service->add($params);
        // 渲染结果
        return $this->success($res, '添加成员成功');
    }

    /**
     * 编辑成员
     *
     * @return array
     * @throws Exception
     */
    public function actionEdit()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['expire_ip', 'string', 'label' => '有效IP地址'],
            ['expire_begin_at', 'date', 'label' => '生效日期', 'format' => 'php:Y-m-d'],
            ['expire_end_at', 'date', 'label' => '失效日期', 'format' => 'php:Y-m-d'],
        ]);
        // 业务处理
        $res = $this->service->edit($params);
        // 渲染结果
        return $this->success($res, '编辑成员成功');
    }

    /**
     * 删除成员
     *
     * @return array
     * @throws Exception
     */
    public function actionDel()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
        ]);
        // 业务处理
        $res = $this->service->del($params);
        // 渲染结果
        return $this->success($res, '删除成员成功');
    }

    /**
     * 查看成员详情
     *
     * @return array
     * @throws Exception
     */
    public function actionView()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
        ]);
        // 业务处理
        $res = $this->service->view($params);
        // 渲染结果
        return $this->success($res, '查看成员详情');
    }

    /**
     * 重置用户密码
     *
     * @return array
     * @throws Exception
     */
    public function actionResetPassword()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid', 'password', 'personalPassword'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['password', 'string', 'label' => '登录密码', 'min' => 6],
            ['personalPassword', UserPasswordValidator::class, 'label' => '私人登录密码'],
        ]);
        // 业务处理
        $res = $this->service->resetPassword($params);
        // 渲染结果
        return $this->success($res, '重置用户密码成功');
    }

    /**
     * 查看用户所有账户信息
     *
     * @return array
     * @throws Exception
     */
    public function actionAccounts()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
        ]);
        // 业务处理
        $res = $this->service->accounts($params);
        // 渲染结果
        return $this->success($res, '查看用户所有账户信息');
    }

    /**
     * 启用或禁用成员
     *
     * @return array
     * @throws Exception
     */
    public function actionChangeStatus()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid', 'status', 'personalPassword'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['status', 'in', 'label' => '启用状态', 'range' => array_keys(TLabelEnable::enableLabels())],
            ['personalPassword', UserPasswordValidator::class, 'label' => '私人登录密码'],
        ]);
        // 业务处理
        $res = $this->service->changeStatus($params);
        // 渲染结果
        return $this->success($res, '启用或禁用成员成功');
    }

    /**
     * 设置用户是否超级管理员
     *
     * @return array
     * @throws Exception
     */
    public function actionChangeSuperStatus()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['uid', 'status', 'personalPassword'], 'required'],
            ['uid', 'exist', 'label' => 'UID', 'targetClass' => User::class, 'targetAttribute' => 'uid'],
            ['status', 'in', 'label' => '启用状态', 'range' => array_keys(TLabelEnable::enableLabels())],
            ['personalPassword', UserPasswordValidator::class, 'label' => '私人登录密码'],
        ]);
        // 业务处理
        $res = $this->service->changeSuperStatus($params);
        // 渲染结果
        return $this->success($res, '设置超级管理员成功');
    }
}