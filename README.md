# yii2-task
yii2实现组件:后管用户管理

# 使用
## 一、配置
### 1.1 登录的默认账号类型 : params -> defaultAccountType，默认 "email"

### 1.2 支持添加的菜单类型 : params -> permissionMenuTypes，默认
```php
'permissionMenuTypes' => [
    'menu'   => '菜单',
    'help'   => '帮助中心',
    'top'    => '顶端菜单',
    'footer' => '底部菜单',
    'button' => '按钮',
    'custom' => '自定义',
]
```

### 1.3 配置控制器 web.php
```php
'controllerMap' => [
    // 后管用户系统
    'login'    => \YiiBackendUser\controllers\LoginController::class,
    'personal' => \YiiBackendUser\controllers\PersonalController::class,
]
```

### 1.4 配置用户登录
```php
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
```