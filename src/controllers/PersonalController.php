<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\controllers;

use Exception;
use Yii;
use YiiBackendUser\actions\ActionUserMenu;
use YiiBackendUser\interfaces\IPersonalService;
use YiiBackendUser\models\User;
use YiiBackendUser\models\UserAccount;
use YiiBackendUser\services\PersonalService;
use YiiHelper\abstracts\RestController;
use YiiHelper\helpers\Req;
use YiiHelper\validators\IdCardValidator;
use YiiHelper\validators\MobileValidator;
use YiiHelper\validators\NameValidator;
use YiiHelper\validators\PasswordValidator;
use YiiHelper\validators\PhoneValidator;
use YiiHelper\validators\QqValidator;
use YiiHelper\validators\SecurityOperateValidator;
use YiiHelper\validators\ZipCodeValidator;
use Zf\Helper\Exceptions\ForbiddenHttpException;
use Zf\Helper\Traits\Models\TLabelEnable;
use Zf\Helper\Traits\Models\TLabelSex;

/**
 * 控制器 ： 个人信息管理
 *
 * Class PersonalController
 * @package YiiBackendUser\controllers
 *
 * @property-read IPersonalService $service
 */
class PersonalController extends RestController
{
    public $serviceInterface = IPersonalService::class;
    public $serviceClass     = PersonalService::class;

    /**
     * @var User
     */
    protected $user;

    /**
     * @inheritDoc
     * @throws \Throwable
     * @throws Exception
     */
    public function init()
    {
        if (Yii::$app->getUser()->getIsGuest()) {
            throw new ForbiddenHttpException('访问无权访问，请先登录');
        }
        $this->user = Yii::$app->getUser()->getIdentity();
        parent::init();
    }

    /**
     * @inheritDoc
     * action 集合
     *
     * @return array
     */
    public function actions()
    {
        return [
            // 登录用户菜单权限
            'menu' => [
                'class' => ActionUserMenu::class,
            ],
        ];
    }

    /**
     * 个人信息
     *
     * @return mixed
     * @throws Exception
     */
    public function actionInfo()
    {
        // 业务处理
        $res = $this->service->info();
        // 渲染结果
        return $this->success($res, '个人信息');
    }

    /**
     * 修改个人信息
     *
     * @return array
     * @throws Exception
     */
    public function actionChangeInfo()
    {
        $uid = Req::getUid();
        // 参数验证和获取
        $params = $this->validateParams([
            ['nickname', 'unique', 'label' => '用户昵称', 'targetClass' => User::class, 'targetAttribute' => 'nickname', 'filter' => ['!=', 'uid', $uid]],
            ['email', 'unique', 'label' => '邮箱账户', 'targetClass' => User::class, 'targetAttribute' => 'email', 'filter' => ['!=', 'uid', $uid]],
            ['mobile', 'unique', 'label' => '手机号码', 'targetClass' => User::class, 'targetAttribute' => 'mobile', 'filter' => ['!=', 'uid', $uid]],
            ['id_card', 'unique', 'label' => '身份证号', 'targetClass' => User::class, 'targetAttribute' => 'id_card', 'filter' => ['!=', 'uid', $uid]],
            ['mobile', MobileValidator::class, 'label' => '手机号码'],
            ['id_card', IdCardValidator::class, 'label' => '身份证号'],
            ['real_name', NameValidator::class, 'label' => '姓名'],
            ['sex', 'in', 'label' => '性别', 'range' => array_keys(TLabelSex::sexLabels())],
            ['avatar', 'string', 'label' => '头像'],
            ['phone', PhoneValidator::class, 'label' => '固定电话'],
            ['qq', QqValidator::class, 'label' => 'QQ'],
            ['birthday', 'date', 'label' => '生日', 'format' => 'php:Y-m-d'],
            ['address', 'string', 'label' => '联系地址'],
            ['zip_code', ZipCodeValidator::class, 'label' => '邮政编码'],
        ]);

        // 业务处理
        $res = $this->service->changeInfo($params);
        // 渲染结果
        return $this->success($res, '修改个人信息');
    }

    /**
     * 修改个人密码
     *
     * @return array
     * @throws Exception
     */
    public function actionResetPassword()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['account_id', 'securityPassword', 'newPassword', 'confirmPassword'], 'required'],
            ['account_id', 'exist', 'label' => '账户ID', 'targetClass' => UserAccount::class, 'targetAttribute' => 'id', 'filter' => ['=', 'uid', $this->user->uid]],
            ['securityPassword', SecurityOperateValidator::class, 'label' => '操作密码'],
            ['newPassword', PasswordValidator::class, 'label' => '新密码'],
            ['confirmPassword', 'compare', 'label' => '确认密码', 'compareAttribute' => 'newPassword'],
        ]);
        // 业务处理
        $res = $this->service->resetPassword($params);
        // 渲染结果
        return $this->success($res, '修改个人密码');
    }

    /**
     * 重置个人安全操作密码
     *
     * @return array
     * @throws Exception
     */
    public function actionResetSecurityPassword()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['password', 'securityPassword'], 'required'],
            ['password', SecurityOperateValidator::class, 'label' => '登录密码', 'method' => 'validatePassword'],
            ['securityPassword', 'string', 'min' => 6, 'label' => '操作密码'],
        ]);
        // 业务处理
        $res = $this->service->resetSecurityPassword($params);
        // 渲染结果
        return $this->success($res, '重置操作密码');
    }

    /**
     * 个人账户信息
     *
     * @return mixed
     * @throws Exception
     */
    public function actionAccounts()
    {
        // 业务处理
        $res = $this->service->accounts();
        // 渲染结果
        return $this->success($res, '个人账户信息');
    }

    /**
     * 添加账户信息
     *
     * @return array
     * @throws Exception
     */
    public function actionAddAccount()
    {
        // 参数验证和获取
        $type  = $this->getParam('type');
        $rules = [
            [['type', 'account'], 'required'],
            ['type', 'in', 'label' => '账户类型', 'range' => array_keys(UserAccount::types())],
            ['is_enable', 'in', 'label' => '启用状态', 'default' => 1, 'range' => array_keys(TLabelEnable::enableLabels())],
            ['account', 'unique', 'label' => '账户', 'targetClass' => UserAccount::class, 'targetAttribute' => 'account', 'filter' => ['=', 'type', $type]],
            ['password', 'string', 'min' => 6, 'label' => '密码'],
        ];
        // 添加账户验证规则
        array_push($rules, UserAccount::getAccountValidatorRule($type));
        // 验证数据并返回验证数据
        $params = $this->validateParams($rules);
        // 业务处理
        $res = $this->service->addAccount($params);
        // 渲染结果
        return $this->success($res, '添加账户信息');
    }

    /**
     * 编辑账户信息
     *
     * @return array
     * @throws Exception
     */
    public function actionEditAccount()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            ['id', 'required'],
            ['id', 'exist', 'label' => '账户ID', 'targetClass' => UserAccount::class, 'targetAttribute' => 'id', 'filter' => ['=', 'uid', $this->user->uid]],
        ]);
        // 业务处理
        $res = $this->service->editAccount($params);
        // 渲染结果
        return $this->success($res, '编辑账户信息');
    }

    /**
     * 修改账户状态（启用|禁用）
     *
     * @return array
     * @throws Exception
     */
    public function actionChangeAccountStatus()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            [['id', 'status'], 'required'],
            ['id', 'exist', 'label' => '账户ID', 'targetClass' => UserAccount::class, 'targetAttribute' => 'id', 'filter' => ['=', 'uid', $this->user->uid]],
            ['status', 'in', 'label' => '启用状态', 'range' => array_keys(TLabelEnable::enableLabels())],
        ]);
        // 业务处理
        $res = $this->service->changeAccountStatus($params);
        // 渲染结果
        return $this->success($res, '修改账户状态');
    }

    /**
     * 删除账户信息
     *
     * @return array
     * @throws Exception
     */
    public function actionDelAccount()
    {
        // 参数验证和获取
        $params = $this->validateParams([
            ['id', 'required'],
            ['id', 'exist', 'label' => '账户ID', 'targetClass' => UserAccount::class, 'targetAttribute' => 'id', 'filter' => ['=', 'uid', $this->user->uid]],
        ]);
        // 业务处理
        $res = $this->service->delAccount($params);
        // 渲染结果
        return $this->success($res, '删除账户信息');
    }
}