<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\bootstraps;


use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use YiiHelper\helpers\Req;
use YiiHelper\traits\TResponse;
use YiiPermission\models\PermissionApi;
use Zf\Helper\Business\IpHelper;
use Zf\Helper\Exceptions\BusinessException;

/**
 * bootstrap组件 : 权限控制
 *
 * Class PermissionBootstrap
 * @package YiiBackendUser\bootstraps
 */
class PermissionBootstrap implements BootstrapInterface
{
    use TResponse;
    const ERROR_CODE_IP_NOT_ALLOW   = "9999800";
    const ERROR_CODE_PATH_NOT_ALLOW = "9999801";

    /**
     * @var bool 是否开启权限检查
     */
    public $openCheck = false;
    /**
     * @var string 未授权的默认提示
     */
    public $errorMessage = 'Unauthorized';
    /**
     * @var array 错误代码的消息列表
     */
    public $errorMessages = [];
    /**
     * @var array 公共路径名单
     */
    public $pubPaths = [];
    /**
     * @var array 路径白名单
     */
    public $whitePaths = [];
    /**
     * @var array ip访问白名单
     */
    public $whiteIps = [];
    /**
     * @var callable 未授权的回调处理函数
     */
    public $denyCallback;

    private $_errMessages = [
        self::ERROR_CODE_IP_NOT_ALLOW   => 'ip不在白名单上',
        self::ERROR_CODE_PATH_NOT_ALLOW => '路径不在白名单上',
    ];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     * @return bool|mixed
     * @throws BusinessException
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        /* @var $app \YiiHelper\extend\Application $this */
        if ($app->getRequest()->getIsConsoleRequest()) {
            // console 应用不检查权限
            return true;
        }
        if (!$this->openCheck) {
            // 权限检查关闭
            return true;
        }
        if ($app->getUser()->getIsSuper()) {
            // 超级管理员不需要检查权限
            return true;
        }
        // 获取用户可访问路径
        if (!$app->getUser()->getIsGuest()) {
            $paths = $app->getUser()->getPermissions();
        } else {
            $paths = PermissionApi::getPublicApi();
        }
        $systemAlias = $app->getSystemAlias();
        if (empty($systemAlias)) {
            $urlPath = $app->getRequest()->getPathInfo();
        } else {
            $urlPath = $systemAlias . '/' . $app->getRequest()->getPathInfo();
        }
        if (in_array($urlPath, $paths)) {
            // 访问
            return true;
        }
        // 公共路径检查
        if ($this->inUrlPath($urlPath, $this->pubPaths)) {
            return true;
        }
        // 路径白名单检查
        if (!$this->inUrlPath($urlPath, $this->whitePaths)) {
            return $this->deny(self::ERROR_CODE_PATH_NOT_ALLOW);
        }
        // ip 白名单检查
        if (IpHelper::inRanges(Req::getUserIp(), $this->whiteIps)) {
            return true;
        }
        return $this->deny(self::ERROR_CODE_IP_NOT_ALLOW);
    }

    /**
     * 判断url-path是否在设置的白名单内
     *
     * @param string $path
     * @param array $urlPaths
     * @return bool
     */
    protected function inUrlPath(string $path, array $urlPaths)
    {
        foreach ($urlPaths as $urlPath) {
            $urlPath = str_replace('/', '\/', trim($urlPath));
            $urlPath = str_replace('*', '(.*)', trim($urlPath));
            if (preg_match('#^' . $urlPath . '$#', $path, $ms)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 无权访问的处理方法
     *
     * @param int $code
     * @param null $message
     * @return mixed
     * @throws BusinessException
     */
    protected function deny(int $code, $message = null)
    {
        if (null === $message) {
            if (isset($this->errorMessages[$code])) {
                $message = $this->errorMessages[$code];
            } else if ($this->_errMessages[$code]) {
                $message = $this->_errMessages[$code];
            } else {
                $message = $this->errorMessage;
            }
        }

        if (is_callable($this->denyCallback)) {
            return call_user_func($this->denyCallback, $code, $message);
        }
        throw new BusinessException($message, $code);
    }
}