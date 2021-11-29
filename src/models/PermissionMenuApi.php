<?php

namespace YiiBackendUser\models;

use YiiBackendUser\models\traits\TBehaviors;
use YiiHelper\abstracts\Model;

/**
 * 模型 : 菜单、按钮拥有的api关联
 * This is the model class for table "portal_permission_menu_path".
 *
 * @property int $id 自增ID
 * @property string $menu_code 菜单、按钮代码
 * @property string $api_code api代码
 * @property string $operate_ip 操作IP
 * @property int $operate_uid 操作UID
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class PermissionMenuApi extends Model
{
    use TBehaviors;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%permission_menu_api}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_code', 'api_code'], 'required'],
            [['operate_uid'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['menu_code', 'api_code'], 'string', 'max' => 50],
            [['operate_ip'], 'string', 'max' => 15],
            [['menu_code', 'api_code'], 'unique', 'targetAttribute' => ['menu_code', 'api_code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '自增ID',
            'menu_code'   => '菜单、按钮代码',
            'api_code'    => 'api代码',
            'operate_ip'  => '操作IP',
            'operate_uid' => '操作UID',
            'created_at'  => '创建时间',
            'updated_at'  => '更新时间',
        ];
    }
}
