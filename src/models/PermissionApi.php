<?php

namespace YiiBackendUser\models;

use YiiBackendUser\models\traits\TBehaviors;
use YiiHelper\abstracts\Model;
use Zf\Helper\Exceptions\BusinessException;

/**
 * This is the model class for table "portal_permission_api".
 *
 * @property int $id 自增ID
 * @property string $code 路径标识
 * @property string $path API路径
 * @property string $remark 路径描述
 * @property string|null $exts 扩展信息
 * @property int $is_public 是否公共路径，公共路径不需要权限
 * @property int $is_enable 是否启用
 * @property string $operate_ip 操作IP
 * @property int $operate_uid 操作UID
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class PermissionApi extends Model
{
    use TBehaviors;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%permission_api}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['exts', 'created_at', 'updated_at'], 'safe'],
            [['is_public', 'is_enable', 'operate_uid'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['path', 'remark'], 'string', 'max' => 200],
            [['operate_ip'], 'string', 'max' => 15],
            [['path'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '自增ID',
            'code'        => '路径标识',
            'path'        => 'API路径',
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

    /**
     * 关联 : 获取路径下配置的所有menu
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getMenus()
    {
        return $this->hasMany(PermissionMenu::class, [
            'code' => 'menu_code'
        ])
            ->alias('menu')
            ->viaTable(PermissionMenuApi::tableName(), [
                'api_code' => 'code'
            ]);
    }

    /**
     * @return int|string|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getMenuCount()
    {
        return $this->getMenus()->count();
    }

    /**
     * 检查是否可以删除
     *
     * @return bool
     * @throws BusinessException
     */
    public function beforeDelete()
    {
        // 删除 menu-api 的关联关系
        PermissionMenuApi::deleteAll(['api_code' => $this->code]);
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
