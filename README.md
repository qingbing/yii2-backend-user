# yii2-task
yii2实现组件:后管用户管理

- 引用组件(配置的使用参考组件)
    - qingbing/yii2-permission

# 使用
## 一、配置

### 1.1 配置控制器 web.php
```php
'controllerMap' => [
    // 后管用户系统
    'login'    => \YiiBackendUser\controllers\LoginController::class,
    'personal' => \YiiBackendUser\controllers\PersonalController::class,
]
```

### 1.2 配置用户登录 web.php
```php
'components' => [
    'user'               => [
        'class'           => \YiiBackendUser\components\User::class,
        'identityClass'   => \YiiBackendUser\models\User::class,
        'enableAutoLogin' => true,
        'identityCookie'  => ['name' => '_identity-portal', 'httpOnly' => true],
        'multiLogin'      => false, // 是否允许多客户端登录，false时，每次登录会重新生成用户的 auth_key
        // 允许登录的账户类型
        'loginTypes'      => [
            \YiiBackendUser\models\UserAccount::TYPE_EMAIL,
            \YiiBackendUser\models\UserAccount::TYPE_USERNAME,
            \YiiBackendUser\models\UserAccount::TYPE_MOBILE,
            \YiiBackendUser\models\UserAccount::TYPE_NAME,
        ],
    ],
],
```

### 1.3 配置后管页面权限控制 web.php
```php
'bootstrap'     => [
    'bootPermission',
],
'components' => [
    'bootPermission' => [
        'class'      => \YiiBackendUser\bootstraps\PermissionBootstrap::class,
        'openCheck'  => true,
        'pubPaths'   => [
            'portal/inner/*', // inner模块接口属于内部服务调用接口，模块内部
            'portal/login/*', // 登录及检查
            'portal/test/*',
            'portal/login/*',
            'portal/public/*'
        ],
        'whiteIps'   => [
            '192.168.1.1',
        ],
        'whitePaths' => [
        ],
    ],
],
```

## 二、对外 action
- \YiiBackendUser\actions\AssignUserRole::class(为用户分配角色)
- \YiiBackendUser\actions\UserMenu::class(登录用户拥有的菜单权限树)
- \YiiBackendUser\actions\UserPermission::class(登录用户拥有的权限)
