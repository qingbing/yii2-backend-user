<?php

namespace YiiBackendUser\models;


use YiiBackendUser\models\traits\TBehaviors;
use YiiHelper\abstracts\Model;

/**
 * 模型 : 角色、菜单关联信息
 * This is the model class for table "portal_permission_role_menu".
 *
 * @property int $id 自增ID
 * @property string $role_code 角色代码
 * @property string $menu_code 菜单、按钮代码
 * @property string $operate_ip 操作IP
 * @property int $operate_uid 操作UID
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class PermissionRoleMenu extends Model
{
    use TBehaviors;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%permission_role_menu}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_code', 'menu_code'], 'required'],
            [['operate_uid'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['role_code', 'menu_code'], 'string', 'max' => 50],
            [['operate_ip'], 'string', 'max' => 15],
            [['role_code', 'menu_code'], 'unique', 'targetAttribute' => ['role_code', 'menu_code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '自增ID',
            'role_code'   => '角色代码',
            'menu_code'   => '菜单、按钮代码',
            'operate_ip'  => '操作IP',
            'operate_uid' => '操作UID',
            'created_at'  => '创建时间',
            'updated_at'  => '更新时间',
        ];
    }
}
