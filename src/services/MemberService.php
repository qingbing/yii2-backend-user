<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace YiiBackendUser\services;


use Exception;
use Yii;
use YiiBackendUser\interfaces\IMemberService;
use YiiBackendUser\models\User;
use YiiBackendUser\models\UserAccount;
use YiiHelper\abstracts\Service;
use YiiHelper\helpers\AppHelper;
use YiiHelper\helpers\Pager;
use YiiHelper\helpers\Req;
use Zf\Helper\Exceptions\BusinessException;
use Zf\Helper\Exceptions\ForbiddenHttpException;
use Zf\Helper\Exceptions\UnsupportedException;
use Zf\Helper\Format;

/**
 * 服务 ： 成员管理
 *
 * Class MemberService
 * @package YiiBackendUser\services
 */
class MemberService extends Service implements IMemberService
{
    /**
     * 搜索时间类型
     *
     * @return array
     */
    public function timeTypeMap(): array
    {
        return User::timeTypes();
    }

    /**
     * 模糊搜索用户
     *
     * @param array $params
     * @return array
     */
    public function searchOption(array $params = []): array
    {
        $query = User::find()
            ->select([
                'uid',
                'nickname',
                'is_enable',
            ])
            ->andFilterWhere(['like', 'nickname', $params['keyword']])
            ->orderBy("is_enable DESC, is_super DESC")
            ->limit($params['limit']);
        if (null !== $params['is_enable']) {
            $query->andWhere(['=', 'is_enable', $params['is_enable']]);
        }
        $res = $query->asArray()
            ->all();
        $R   = [];
        foreach ($res as $re) {
            $R[$re['uid']] = "{$re['nickname']}" . ($re['is_enable'] ? "" : "({$re['停用']})");
        }
        return $R;
    }

    /**
     * 成员列表
     *
     * @param array|null $params
     * @return array
     */
    public function list(array $params = []): array
    {
        $query = User::find()
            ->orderBy('uid DESC');
        // 等于查询
        $this->attributeWhere($query, $params, [
            'uid',
            'nickname',
            'real_name',
            'sex',
            'email',
            'qq',
            'is_enable',
            'is_super',
            'refer_uid',
        ]);
        // like 查询
        $this->likeWhere($query, $params, ['mobile', 'id_card']);
        // 是否有效查询
        if (isset($params['isExpire'])) {
            $this->expireWhere($query, $params['isExpire'], 'expire_begin_date', 'expire_end_date');
        }
        if ("" !== $params['time_type'] && null !== $params['time_type']) {
            if ($params['time_type'] == User::TIME_TYPE_EXPIRE) {
                $query->andFilterWhere(['>=', 'expire_begin_date', $params['start_at']])
                    ->andFilterWhere(['<=', 'expire_end_date', $params['end_at']]);
            } else if ($params['time_type'] == User::TIME_TYPE_LOGIN) {
                $query->andFilterWhere(['>=', 'last_login_at', $params['start_at']])
                    ->andFilterWhere(['<=', 'last_login_at', $params['end_at']]);
            } else if ($params['time_type'] == User::TIME_TYPE_REGISTER) {
                $query->andFilterWhere(['>=', 'register_at', $params['start_at']])
                    ->andFilterWhere(['<=', 'register_at', $params['end_at']]);
            }
        }
        return Pager::getInstance()->pagination($query, $params['pageNo'], $params['pageSize']);
    }

    /**
     * 添加成员
     *
     * @param array $params
     * @return bool
     * @throws \Throwable
     * @throws Exception
     */
    public function add(array $params): bool
    {
        $userAccount    = new UserAccount();
        $user           = new User();
        $userAttributes = $params;
        // 删除账户和密码
        unset($userAttributes['account'], $userAttributes['password']);
        $user->setFilterAttributes($userAttributes);
        $user->refer_uid = Req::getUid();
        $accountType     = UserAccount::getDefaultAccountType();

        $userAccount->type     = $accountType;
        $userAccount->password = $user->generatePassword($params['password']);
        if (!empty($params['account'])) {
            // 直接给出账号
            $userAccount->account = $params['account'];
            switch ($accountType) {
                case UserAccount::TYPE_EMAIL   : // 邮箱
                    $user->email = $params['account'];
                    break;
                case UserAccount::TYPE_MOBILE  : // 手机号
                    $user->mobile = $params['account'];
                    break;
                case UserAccount::TYPE_NAME    : // 姓名
                    $user->real_name = $params['account'];
                    break;
                case UserAccount::TYPE_USERNAME: // 用户名
                default:
                    break;
            }
        } else {
            // 间接给出账号
            switch ($accountType) {
                case UserAccount::TYPE_EMAIL   : // 邮箱
                    $userAccount->account = $params['email'];
                    $user->email          = $params['email'];
                    break;
                case UserAccount::TYPE_MOBILE  : // 手机号
                    $userAccount->account = $params['mobile'];
                    $user->mobile         = $params['mobile'];
                    break;
                case UserAccount::TYPE_NAME    : // 姓名
                    $userAccount->account = $params['real_name'];
                    $user->real_name      = $params['real_name'];
                    break;
                case UserAccount::TYPE_USERNAME: // 用户名
                default:
                    $userAccount->account = $params['username'];
                    break;
            }
        }
        if (empty($userAccount->account)) {
            throw new BusinessException("未设置账户信息");
        }
        Yii::$app->getDb()->transaction(function () use ($user, $userAccount) {
            $user->saveOrException();
            $userAccount->link('user', $user);
            $userAccount->saveOrException();
        });
        return true;
    }

    /**
     * 编辑成员
     *
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function edit(array $params): bool
    {
        $model = $this->getModel($params);
        unset($params['uid']);
        $model->setFilterAttributes($params);
        return $model->saveOrException();
    }

    /**
     * 删除成员
     *
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function del(array $params): bool
    {
        throw new UnsupportedException("该功能未开通，建议使用禁用功能");
    }

    /**
     * 查看成员详情
     *
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function view(array $params)
    {
        return $this->getModel($params, false);
    }

    /**
     * 重置用户密码
     *
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function resetPassword(array $params = []): bool
    {
        $model       = $this->getModel($params);
        $userAccount = UserAccount::findOne([
            'id'  => $params['account_id'],
            'uid' => $model->uid,
        ]);
        if (empty($userAccount)) {
            throw new BusinessException("账户信息不匹配");
        }
        $userAccount->password = $model->generatePassword($params['password']);
        return $userAccount->saveOrException();
    }

    /**
     * 查看用户所有账户信息
     *
     * @param array|null $params
     * @return array
     * @throws Exception
     */
    public function accounts(array $params = []): array
    {
        return $this->getModel($params, false)->accounts;
    }

    /**
     * 启用或禁用成员
     *
     * @param array|null $params
     * @return bool
     * @throws Exception
     */
    public function changeStatus(array $params = []): bool
    {
        $model            = $this->getModel($params);
        $model->is_enable = $params['status'];
        return $model->saveOrException();
    }

    /**
     * 设置用户是否超级管理员
     *
     * @param array|null $params
     * @return bool
     * @throws Exception
     */
    public function changeSuperStatus(array $params = []): bool
    {
        if (!AppHelper::app()->getUser()->getIsSuper()) {
            throw new ForbiddenHttpException("您不是超级管理员不能使用该功能");
        }
        $model           = $this->getModel($params);
        $model->is_super = $params['status'];
        return $model->saveOrException();
    }

    /**
     * 获取当前操作模型
     *
     * @param array $params
     * @param bool $isOperate
     * @return User
     * @throws Exception
     */
    protected function getModel(array $params, $isOperate = true): User
    {
        $model = User::findOne([
            'uid' => $params['uid'] ?? null
        ]);
        if (null === $model) {
            throw new BusinessException("用户不存在");
        }
        if ($isOperate && $model->uid == Req::getUid()) {
            throw new ForbiddenHttpException("不能在该页面操作自己的信息");
        }
        return $model;
    }
}