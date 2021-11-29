<?php

namespace YiiBackendUser\models;

use YiiBackendUser\models\traits\TBehaviors;
use YiiHelper\abstracts\Model;

/**
 * 模型 : 用户、角色关联信息
 * This is the model class for table "portal_permission_user_role".
 *
 * @property int $id 自增ID
 * @property int $uid 用户ID
 * @property string $role_code 角色代码
 * @property string $operate_ip 操作IP
 * @property int $operate_uid 操作UID
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class PermissionUserRole extends Model
{
    use TBehaviors;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%permission_user_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'role_code'], 'required'],
            [['uid', 'operate_uid'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['role_code'], 'string', 'max' => 50],
            [['operate_ip'], 'string', 'max' => 15],
            [['uid', 'role_code'], 'unique', 'targetAttribute' => ['uid', 'role_code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '自增ID',
            'uid'         => '用户ID',
            'role_code'   => '角色代码',
            'operate_ip'  => '操作IP',
            'operate_uid' => '操作UID',
            'created_at'  => '创建时间',
            'updated_at'  => '更新时间',
        ];
    }
}
