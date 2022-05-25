<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\models;


use Yii;
use YiiBackendUser\services\loginType\drivers\Email;
use YiiBackendUser\services\loginType\drivers\Mobile;
use YiiBackendUser\services\loginType\drivers\Name;
use YiiBackendUser\services\loginType\drivers\Username;
use YiiHelper\abstracts\Model;
use YiiHelper\helpers\AppHelper;
use YiiHelper\validators\MobileValidator;
use YiiHelper\validators\NameValidator;
use YiiHelper\validators\UsernameValidator;
use Zf\Helper\Exceptions\CustomException;

/**
 * This is the model class for table "{{%user_account}}".
 *
 * @property int $id 自增ID
 * @property int $uid 用户ID
 * @property string $type 账户类型:username,email,phone,name,weixin,qq等
 * @property string $account 登录账户
 * @property string $password 密码
 * @property string $auth_key 登录的auth_key
 * @property int $is_enable 启用状态
 * @property int $login_times 登录次数
 * @property string $last_login_ip 最后登录IP
 * @property string $last_login_at 最后登录时间
 * @property string $register_at 注册或添加时间
 * @property string $updated_at 最后数据更新时间
 */
class UserAccount extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_account}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'type', 'account'], 'required'],
            [['uid', 'is_enable', 'login_times'], 'integer'],
            [['last_login_at', 'register_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 20],
            [['account'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['last_login_ip'], 'string', 'max' => 15],
            [['type', 'account'], 'unique', 'targetAttribute' => ['type', 'account']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => '自增ID',
            'uid'           => '用户ID',
            'type'          => '账户类型',
            'account'       => '登录账户',
            'password'      => '密码',
            'auth_key'      => '登录的auth_key',
            'is_enable'     => '启用状态',
            'login_times'   => '登录次数',
            'last_login_ip' => '登录IP',
            'last_login_at' => '登录时间',
            'register_at'   => '注册时间',
            'updated_at'    => '更新时间',
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
        unset($fields['password'], $fields['auth_key']);
        return $fields;
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
     * 关联 : 用户信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, [
            'uid' => 'uid',
        ]);
    }

    const TYPE_USERNAME = 'username';
    const TYPE_EMAIL    = 'email';
    const TYPE_MOBILE   = 'mobile';
    const TYPE_NAME     = 'name';

    /**
     * @var array 程序支持额登录类型，要增减可以重载该变量
     */
    protected static $_types = [
        self::TYPE_USERNAME => '用户名',
        self::TYPE_EMAIL    => '邮箱',
        self::TYPE_MOBILE   => '手机号',
        self::TYPE_NAME     => '姓名',
    ];
    /**
     * @var array 程序支持类型的登录服务，要增减可以重载该变量
     */
    protected static $_serviceMaps = [
        self::TYPE_EMAIL    => Email::class,
        self::TYPE_USERNAME => Username::class,
        self::TYPE_MOBILE   => Mobile::class,
        self::TYPE_NAME     => Name::class,
    ];

    /**
     * 系统支持的登录类型
     *
     * @return array
     *  return [
     *      self::TYPE_USERNAME => '用户名',
     *      self::TYPE_EMAIL    => '邮箱',
     *      self::TYPE_MOBILE   => '手机号',
     *      self::TYPE_NAME     => '姓名',
     *  ];
     *
     * @throws CustomException
     */
    public static function types(): array
    {
        $supportTypes = [];
        foreach (\Yii::$app->user->loginTypes as $type) {
            if (!isset(static::$_types[$type])) {
                throw new CustomException(replace('不支持的登录类型"{type}"', [
                    '{type}' => $type
                ]));
            }
            $supportTypes[$type] = static::$_types[$type];
        }
        if (empty($supportTypes)) {
            throw new CustomException("未配置系统支持的登录类型");
        }
        return $supportTypes;
    }

    /**
     * 系统支持的登录类型对应服务
     *
     * @return array
     * @throws CustomException
     */
    public static function serviceMaps()
    {
        $supportServiceMaps = [];
        foreach (\Yii::$app->user->loginTypes as $type) {
            if (!isset(static::$_serviceMaps[$type])) {
                throw new CustomException(replace('不支持的登录类型"{type}"', [
                    '{type}' => $type
                ]));
            }
            $supportServiceMaps[$type] = static::$_serviceMaps[$type];
        }
        if (empty($supportServiceMaps)) {
            throw new CustomException('未配置系统支持的登录类型');
        }
        return $supportServiceMaps;
    }

    /**
     * 后管获取默认的登录账号
     *
     * @return mixed
     */
    public static function getDefaultAccountType()
    {
        return AppHelper::app()->getParam('defaultAccountType', 'email');
    }

    /**
     * 根据账户类型获取账户类型的验证规则
     *
     * @param string $type
     * @param string $field
     * @return array
     * @throws CustomException
     */
    public static function getAccountValidatorRule(string $type, $field = 'account'): array
    {
        switch ($type) {
            case static::TYPE_USERNAME :// 'username';
                $rule = [$field, UsernameValidator::class];
                break;
            case static::TYPE_EMAIL    :// 'email';
                $rule = [$field, 'email'];
                break;
            case static::TYPE_MOBILE   :// 'mobile';
                $rule = [$field, MobileValidator::class];
                break;
            case static::TYPE_NAME     :// 'name';
                $rule = [$field, NameValidator::class];
                break;
            default:
                throw new CustomException("不支持的账户类型");
        }
        return $rule;
    }
}