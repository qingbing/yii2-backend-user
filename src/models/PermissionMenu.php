<?php

namespace YiiBackendUser\models;

use YiiBackendUser\models\traits\TBehaviors;
use YiiHelper\abstracts\Model;
use YiiHelper\helpers\AppHelper;

/**
 * 模型 : 前端url路径信息
 * This is the model class for table "portal_permission_menu".
 *
 * @property int $id 自增ID
 * @property string $type 类型[menu:菜单,footer:底部菜单,top:顶端菜单,button:按钮]
 * @property string $path 菜单路径
 * @property string $parent_code 上级标识
 * @property string $code 菜单标识
 * @property string $remark 路径描述
 * @property string|null $exts 扩展信息
 * @property int $is_public 是否公共路径，公共路径不需要权限
 * @property int $is_enable 是否启用
 * @property string $operate_ip 操作IP
 * @property int $operate_uid 操作UID
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property-read PermissionApi[] $apis
 * @property-read int $apiCount
 * @property-read PermissionRole[] $roles
 * @property-read int $roleCount
 */
class PermissionMenu extends Model
{
    use TBehaviors;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%permission_menu}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'code'], 'required'],
            [['exts', 'created_at', 'updated_at'], 'safe'],
            [['is_public', 'is_enable', 'operate_uid'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['path', 'remark'], 'string', 'max' => 200],
            [['parent_code', 'code'], 'string', 'max' => 50],
            [['operate_ip'], 'string', 'max' => 15],
            [['type', 'path'], 'unique', 'targetAttribute' => ['type', 'path']],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '自增ID',
            'type'        => '类型[menu:菜单,footer:底部菜单,top:顶端菜单,button:按钮]',
            'path'        => '菜单路径',
            'parent_code' => '上级标识',
            'code'        => '菜单标识',
            'remark'      => '路径描述',
            'exts'        => '扩展信息',
            'is_public'   => '是否公共路径，公共路径不需要权限',
            'is_enable'   => '是否启用',
            'operate_ip'  => '操作IP',
            'operate_uid' => '操作UID',
            'created_at'  => '创建时间',
            'updated_at'  => '更新时间',
        ];
    }

    const TYPE_MENU   = 'menu'; // 菜单
    const TYPE_HELP   = 'help'; // 帮助中心
    const TYPE_TOP    = 'top'; // 顶端菜单
    const TYPE_FOOTER = 'footer'; // 底部菜单
    const TYPE_BUTTON = 'button'; // 按钮
    const TYPE_CUSTOM = 'custom'; // 自定义

    /**
     * 菜单类型
     *
     * @return array
     */
    public static function types()
    {
        return AppHelper::app()->getParam('permissionMenuTypes', [
            self::TYPE_MENU   => '菜单',
            self::TYPE_HELP   => '帮助中心',
            self::TYPE_TOP    => '顶端菜单',
            self::TYPE_FOOTER => '底部菜单',
            self::TYPE_BUTTON => '按钮',
            self::TYPE_CUSTOM => '自定义',
        ]);
    }

    /**
     * 关联 : 获取菜单下配置的所有api
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getApis()
    {
        return $this->hasMany(PermissionApi::class, [
            'code' => 'api_code'
        ])
            ->alias('api')
            ->viaTable(PermissionMenuApi::tableName(), [
                'menu_code' => 'code'
            ]);
    }

    /**
     * @return int|string|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getApiCount()
    {
        return $this->getApis()->count();
    }

    /**
     * 关联 : 获取菜单下配置的所有角色
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getRoles()
    {
        return $this->hasMany(PermissionRole::class, [
            'code' => 'role_code',
        ])
            ->alias('role')
            ->viaTable(PermissionRoleMenu::tableName(), [
                'menu_code' => 'code'
            ]);
    }

    /**
     * @return int|string|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getRoleCount()
    {
        return $this->getRoles()->count();
    }

    /**
     * 检查是否可以删除
     *
     * @return bool
     */
    public function beforeDelete()
    {
        // 删除 menu-api 的关联关系
        PermissionMenuApi::deleteAll(['menu_code' => $this->code]);
        // 删除 role-menu 的关联关系
        PermissionRoleMenu::deleteAll(['menu_code' => $this->code]);
        return parent::beforeDelete();
    }

    /**
     * 获取公用的路径
     *
     * @param bool $isOptions
     * @param int $isEnable
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getPublicApi($isOptions = true, $isEnable = 1)
    {
        $query = static::find()
            ->andWhere(['=', 'is_public', 1]);
        if ($isEnable) {
            $query->andWhere(['=', 'is_enable', $isEnable]);
        }
        if ($isOptions) {
            $res = $query->select(['code', 'path'])
                ->asArray()
                ->all();
            return array_column($res, 'path', 'code');
        }
        return $query->all();
    }
}
