<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\models;


use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use YiiBackendUser\components\User as UserComponent;
use YiiHelper\abstracts\Model;
use YiiHelper\behaviors\DefaultBehavior;
use YiiHelper\behaviors\IpBehavior;
use YiiPermission\models\PermissionApi;
use YiiPermission\models\PermissionMenu;
use YiiPermission\models\PermissionMenuApi;
use YiiPermission\models\PermissionRole;
use YiiPermission\models\PermissionRoleMenu;
use YiiPermission\models\PermissionUserRole;
use Zf\Helper\Exceptions\BusinessException;
use Zf\Helper\Util;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $uid 自增ID
 * @property string $nickname 用户昵称
 * @property string $real_name 姓名
 * @property string $password 密码
 * @property string $security_password 安全操作密码
 * @property string $auth_key 登录的auth_key
 * @property int $sex 性别[0:保密,1:男士,2:女士]
 * @property string $avatar 头像
 * @property string $email 邮箱账户
 * @property string $mobile 手机号码
 * @property string $phone 固定电话
 * @property string $qq QQ
 * @property string $id_card 身份证号
 * @property string $birthday 生日
 * @property string $address 联系地址
 * @property string $zip_code 邮政编码
 * @property int $is_enable 用户启用状态
 * @property int $is_super 是否超级用户
 * @property int $refer_uid 引荐人或添加人UID
 * @property string $expire_ip 有效IP地址
 * @property string $expire_begin_date 生效日期
 * @property string $expire_end_date 失效日期
 * @property int $login_times 登录次数
 * @property string $last_login_ip 最后登录IP
 * @property string $last_login_at 最后登录时间
 * @property string $register_ip 注册或添加IP
 * @property string $register_at 注册或添加时间
 * @property string $updated_at 最后数据更新时间
 *
 * @property-read PermissionRole[] $roles 用户的有效角色
 * @property-read UserAccount[] $accounts 用户的有效角色
 */
class User extends Model implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nickname'], 'required'],
            [['sex', 'is_enable', 'is_super', 'refer_uid', 'login_times'], 'integer'],
            [['birthday', 'expire_begin_date', 'expire_end_date', 'last_login_at', 'register_at', 'updated_at'], 'safe'],
            [['nickname'], 'string', 'max' => 50],
            [['real_name'], 'string', 'max' => 30],
            [['password', 'security_password'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['avatar'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 100],
            [['mobile', 'phone', 'qq', 'last_login_ip', 'register_ip'], 'string', 'max' => 15],
            [['id_card'], 'string', 'max' => 18],
            [['address', 'expire_ip'], 'string', 'max' => 255],
            [['zip_code'], 'string', 'max' => 6],
            [['nickname'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'uid'               => '自增ID',
            'nickname'          => '用户昵称',
            'real_name'         => '姓名',
            'password'          => '密码',
            'security_password' => '安全操作密码',
            'auth_key'          => '登录的auth_key',
            'sex'               => '性别[0:保密,1:男士,2:女士]',
            'avatar'            => '头像',
            'email'             => '邮箱账户',
            'mobile'            => '手机号码',
            'phone'             => '固定电话',
            'qq'                => 'QQ',
            'id_card'           => '身份证号',
            'birthday'          => '生日',
            'address'           => '联系地址',
            'zip_code'          => '邮政编码',
            'is_enable'         => '用户启用状态',
            'is_super'          => '是否超级用户',
            'refer_uid'         => '引荐人或添加人UID',
            'expire_ip'         => '有效IP地址',
            'expire_begin_date' => '生效日期',
            'expire_end_date'   => '失效日期',
            'login_times'       => '登录次数',
            'last_login_ip'     => '登录IP',
            'last_login_at'     => '登录时间',
            'register_ip'       => '注册IP',
            'register_at'       => '注册时间',
            'updated_at'        => '更新时间',
        ];
    }

    /**
     * 绑定 behavior
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class'      => DefaultBehavior::class,
                'type'       => DefaultBehavior::TYPE_DATE,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['birthday'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['birthday'],
                ],
            ],
            [
                'class'      => DefaultBehavior::class,
                'type'       => DefaultBehavior::TYPE_DATETIME,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['expire_begin_date', 'expire_end_date', 'last_login_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['expire_begin_date', 'expire_end_date', 'last_login_at'],
                ],
            ],
            [
                'class'      => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'register_ip',
                ],
            ],
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne([
            'uid'       => $id,
            'is_enable' => IS_ENABLE_YES,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

    /**
     * @inheritDoc
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not support');
    }

    /**
     * 生成 db 密码
     *
     * @param string $pass
     * @return string
     * @throws \yii\base\Exception
     */
    public function generatePassword(string $pass)
    {
        return Yii::$app->getSecurity()->generatePasswordHash($pass);
    }

    /**
     * 验证 db 登录密码是否正确
     *
     * @param string $pass
     * @return bool
     */
    public function validatePassword(string $pass)
    {
        return Yii::$app->getSecurity()->validatePassword($pass, $this->password);
    }

    /**
     * 验证 security 密码是否正确
     *
     * @param string $pass
     * @return bool
     */
    public function validateSecurityPassword(string $pass)
    {
        return Yii::$app->getSecurity()->validatePassword($pass, $this->security_password);
    }

    /**
     * 创建登录auth_key
     *
     * @return $this
     */
    public function generateAuthKey()
    {
        $this->auth_key = Util::uniqid();
        return $this;
    }

    const TIME_TYPE_EXPIRE   = 'expire';
    const TIME_TYPE_REGISTER = 'register';
    const TIME_TYPE_LOGIN    = 'login';

    /**
     * 时间类型标签
     *
     * @return array
     */
    public static function timeTypes()
    {
        return [
            static::TIME_TYPE_EXPIRE   => '有效时间', // expire
            static::TIME_TYPE_REGISTER => '注册时间', // register
            static::TIME_TYPE_LOGIN    => '登录时间', // login
        ];
    }

    /**
     * 设置常规模型的 toArray 字段
     *
     * @return array|false
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['password'], $fields['security_password'], $fields['auth_key']);
        return $fields;
    }

    /**
     * 关联 : 用户账户
     *
     * @return ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(UserAccount::class, [
            'uid' => 'uid',
        ])
            ->alias('account')
            ->orderBy("is_enable DESC, id ASC");
    }

    /**
     * @inheritDoc
     * @throws BusinessException
     */
    public function beforeDelete()
    {
        if (count($this->accounts)) {
            throw new BusinessException("用户下还有账户，不能删除");
        }
        return parent::beforeDelete();
    }

    /**
     * @var UserAccount
     */
    private $_loginAccount;

    /**
     * 登录时设置登录的账户
     *
     * @param UserAccount $account
     * @return $this
     */
    public function setLoginAccount(UserAccount $account)
    {
        $this->_loginAccount = $account;
        return $this;
    }

    /**
     * 获取登录的的账户信息
     *
     * @return UserAccount|null
     */
    public function getLoginAccount()
    {
        if (null === $this->_loginAccount) {
            // 主要用于登录后期获取登录账户，由于在 getIsGuest 中会验证 auth_key，所以该函数不是能是 getIsGuest 判断是否登录后再获取消息
            $uid     = $this->uid;
            $type    = Yii::$app->getSession()->get(UserComponent::LOGIN_TYPE_KEY);
            $account = Yii::$app->getSession()->get(UserComponent::LOGIN_ACCOUNT_KEY);
            if (empty($uid) || empty($type) || empty($account)) {
                return null;
            }
            $this->_loginAccount = $this->getUserAccount($uid, $type, $account);
        }
        return $this->_loginAccount;
    }

    /**
     * 查询用户账户信息
     *
     * @param $uid
     * @param string $type
     * @param string $account
     * @return UserAccount
     */
    protected function getUserAccount($uid, string $type, string $account): UserAccount
    {
        return UserAccount::findOne([
            'uid'     => $uid,
            'type'    => $type,
            'account' => $account,
        ]);
    }

    /**
     * 关联 : 获取用户已经分配的角色
     *
     * @param bool $onlyValid
     * @param bool $forQuery
     * @return array|ActiveQuery|$this[]
     * @throws InvalidConfigException
     */
    public function getRoles($onlyValid = true, $forQuery = false)
    {
        if ($this->is_super) {
            // 超级管理员
            $query = PermissionRole::find()
                ->alias('role');
        } else {
            // 普通管理员
            $query = $this->hasMany(PermissionRole::class, [
                'code' => 'role_code',
            ])
                ->alias('role')
                ->viaTable(PermissionUserRole::tableName(), [
                    'uid' => 'uid'
                ]);
        }
        if ($onlyValid) {
            $query->andWhere(['=', 'role.is_enable', 1]);
        }
        return $forQuery ? $query : $query->all();
    }

    /**
     * 获取用户的权限菜单
     *
     * @param string $menuType
     * @param bool $withButton
     * @param string $direction
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     */
    public function getMenus($menuType = null, $withButton = false, $direction = DIRECTION_NONE)
    {
        /**
         * 获取用户的角色
         */
        $roleRes = $this->getRoles(true, true)
            ->select(['id', 'code', 'name'])
            ->asArray()
            ->all();
        $roles   = array_column($roleRes, 'name', 'code');
        $query   = PermissionMenu::find()
            ->alias('menu')
            ->select(['menu.type', 'menu.code', 'menu.parent_code'])
            ->andWhere(['=', 'menu.is_enable', 1])
            ->indexBy('code');
        if (!empty($menuType)) {
            if ($withButton) {
                $query->andWhere(['in', 'type', [$menuType, PermissionMenu::TYPE_BUTTON]]);
            } else {
                $query->andWhere(['=', 'type', $menuType]);
            }
        }
        /**
         * 获取用户的菜单
         */
        // 公共菜单
        $pubMenu = (clone $query)
            ->andWhere(['=', 'menu.is_public', 1])
            ->asArray()
            ->all();
        // 权限菜单
        $mainQuery = (clone $query);
        if (!$this->is_super) {
            $mainQuery->andWhere(['in', 'rm.role_code', array_keys($roles)]);
        }
        $menus = $mainQuery
            ->leftJoin(PermissionRoleMenu::tableName() . 'as rm', 'rm.menu_code=menu.code')
            ->asArray()
            ->all();
        $menus = $downMenus = $upMenus = array_merge($menus, $pubMenu);
        if (DIRECTION_BOTH || DIRECTION_DOWN) {
            // 向下查找菜单
            $count = count($menus);
            while (true) {
                $downMenus = (clone $query)
                    ->andWhere(['in', 'parent_code', array_column($upMenus, 'code')])
                    ->asArray()
                    ->all();
                $menus     = array_merge($menus, $downMenus);
                $newCount  = count($menus);
                if ($newCount === $count) {
                    break;
                }
                $count = $newCount;
            }
        }
        if (DIRECTION_BOTH || DIRECTION_UP) {
            // 向上查找菜单
            $count = count($menus);
            while (true) {
                $upMenus  = (clone $query)
                    ->andWhere(['in', 'code', array_column($upMenus, 'parent_code')])
                    ->asArray()
                    ->all();
                $menus    = array_merge($menus, $upMenus);
                $newCount = count($menus);
                if ($newCount === $count) {
                    break;
                }
                $count = $newCount;
            }
        }
        // 向下查找子菜单
        return $menus;
    }

    /**
     * 获取用户的权限，包括 角色、菜单、路径
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getPermissions()
    {
        /**
         * 获取用户的角色
         */
        $roleRes = $this->getRoles(true, true)
            ->select(['id', 'code', 'name'])
            ->asArray()
            ->all();
        $roles   = array_column($roleRes, 'name', 'code');
        /**
         * 获取用户的菜单
         */
        $menus = $this->getMenus(null, true, DIRECTION_BOTH);
        /**
         * 获取用户的api
         */
        $apiRes = PermissionApi::find()
            ->alias('api')
            ->select(['api.code', 'api.path'])
            ->leftJoin(PermissionMenuApi::tableName() . 'as ma', 'ma.api_code=api.code')
            ->andWhere(['=', 'api.is_enable', 1])
            ->andWhere(['in', 'ma.menu_code', array_keys($menus)])
            ->asArray()
            ->all();
        $paths  = array_column($apiRes, 'path', 'code');
        return [
            'roles' => $roles,
            'menus' => $menus,
            'paths' => $paths,
        ];
    }
}