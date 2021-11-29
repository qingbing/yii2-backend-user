<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\models\traits;

use yii\db\ActiveRecord;
use YiiHelper\behaviors\IpBehavior;
use YiiHelper\behaviors\UidBehavior;

/**
 * 片段 : 用户和ip
 *
 * Trait TBehaviors
 * @package YiiBackendUser\models\traits
 */
trait TBehaviors
{
    /**
     * 绑定 behavior : 用户和ip
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class'      => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'operate_ip',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'operate_ip',
                ],
            ],
            [
                'class'      => UidBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'operate_uid',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'operate_uid',
                ],
            ],
        ];
    }
}