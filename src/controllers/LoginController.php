<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\controllers;


use Exception;
use Yii;
use yii\validators\EmailValidator;
use YiiBackendUser\interfaces\ILoginService;
use YiiBackendUser\models\UserAccount;
use YiiBackendUser\services\LoginService;
use YiiHelper\abstracts\RestController;
use YiiHelper\validators\MobileValidator;
use YiiHelper\validators\NameValidator;
use YiiHelper\validators\UsernameValidator;
use Zf\Helper\Exceptions\BusinessException;

/**
 * 控制器 ： 用户登录
 *
 * Class LoginController
 * @package YiiBackendUser\controllers
 *
 * @property-read ILoginService $service
 */
class LoginController extends RestController
{
    /**
     * @var string 控制器服务类接口
     */
    public $serviceInterface = ILoginService::class;
    /**
     * @var string 控制器服务类名
     */
    public $serviceClass = LoginService::class;
    /**
     * @var array 开通登录的方式
     */
    protected $accountRules = [
        UserAccount::TYPE_USERNAME => ['account', UsernameValidator::class],
        UserAccount::TYPE_EMAIL    => ['account', EmailValidator::class],
        UserAccount::TYPE_MOBILE   => ['account', MobileValidator::class],
        UserAccount::TYPE_NAME     => ['account', NameValidator::class],
    ];

    /**
     * 账户登录
     *
     * @return array
     * @throws Exception
     */
    public function actionSignIn()
    {
        // 登录类型获取
        $type = $this->getParam('type', 'email');
        if (!isset($this->accountRules[$type]) || !in_array($type, Yii::$app->getUser()->loginTypes)) {
            throw new BusinessException('不支持的登录方式');
        }
        // 校验规则组装
        $rules = [
            ['account', 'required', 'label' => '登录账户'],
            ['password', 'required', 'label' => '登录密码'],
            ['type', 'safe', 'label' => '登录类型'],
        ];
        array_push($rules, $this->accountRules[$type]);
        // 参数校验并获取规则字段
        $params = $this->validateParams($rules);
        // 数据处理
        $res = $this->service->signIn($params);
        //结果返回渲染
        return $this->success($res, '登录成功');
    }

    /**
     * 用户退出登录
     *
     * @return array
     * @throws Exception
     */
    public function actionSignOut()
    {
        // 数据处理
        $res = $this->service->signOut();
        //结果返回渲染
        return $this->success($res, '退出成功');
    }

    /**
     * 判断是否用户登录
     *
     * @return array
     * @throws Exception
     */
    public function actionIsLogin()
    {
        // 数据获取
        $res = $this->service->isLogin();
        //结果返回渲染
        return $this->success($res, '用户登录判断');
    }

    /**
     * 支持的登录类型
     *
     * @return array
     * @throws Exception
     */
    public function actionGetSupportTypes()
    {
        // 数据获取
        $res = $this->service->getSupportTypes();
        //结果返回渲染
        return $this->success($res, '支持的登录类型');
    }
}