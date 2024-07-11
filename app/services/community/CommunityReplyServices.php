<?php

// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\services\community;

use app\dao\community\CommunityReplyDao;
use app\services\BaseServices;
use app\services\system\RelevanceServices;
use Carbon\Exceptions\InvalidDateException;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Route;
use think\Model;

/**
 * Class CommunityReplyServices
 * @packge app\services\community
 * @date : 2024/1/10 11:03
 * @author: Trump
 * @mixin CommunityReplyDao
 */
class CommunityReplyServices extends BaseServices
{
    /**
     * @var CommunityReplyDao
     */
    protected $dao;

    /**
     * @param CommunityReplyDao $dao
     */
    public function __construct(CommunityReplyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 11:19
     * @author: Trump
     */
    public function getList(array $where): array
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $query = $this->dao
            ->search($where)
            ->with([
            'community' => function ($query) {
                $query->field('community_id,title');
            },
            'author' => function ($query) {
                $query->field('uid,nickname,avatar');
            },
            'reply' => function ($query) {
                $query->field('uid,nickname,avatar');
            },
            'hasReply' => function ($query) {
                $query->field('pid, reply_id, status');
            },
        ]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();

        return compact('count', 'list');
    }

    /**
     * @param array $where
     * @param $userInfo
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 18:12
     * @author: Trump
     */
    public function getApiList(array $where, $userInfo): array
    {
        [$page, $limit] = $this->getPageValue();
        $make = app()->make(CommunityServices::class);

        $where_['community_id'] = $where['community_id'];
        $where_ = CommunityServices::IS_SHOW_WHERE;

        if ($userInfo && $make->uidExists((int)$where['community_id'], $userInfo->uid)) {
            $where_ = ['is_del' => 0];
        }

        $where_['community_id'] = $where['community_id'];

        if ( ! $make->getWhereCount($where_)) throw new ValidateException('内容不存在，可能被删删除了哦～');

        $where['status'] = 1;
        $all = $this->dao->getSearch($where)->count();
        $start = $this->dao->getSearch($where)->sum('count_start');
        $where['pid'] = 0;

        $query = $this->dao->getSearch($where)
            ->order('create_time DESC')
            ->hidden(['refusal'])
            ->with([
                'author' => function ($query) {
                    $query->field('uid,nickname,avatar');
                },
                'is_start' => function ($query) use ($userInfo) {
                    $query->where('left_id', $userInfo->uid ?? null);
                },
                'children' => [
                    'author' => function ($query) {
                        $query->field('uid,nickname,avatar');
                    },
                    'reply' => function ($query) {
                        $query->field('uid,nickname,avatar');
                    },
                    'is_start' => function ($query) use ($userInfo) {
                        $query->where('left_id', $userInfo->uid ?? null);
                    }
                ],
            ]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('all', 'start', 'count', 'list');
    }


    /**
     * 发表评论
     * @param int $replyId
     * @param array $data
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function create(int $replyId, array $data)
    {
        $make = app()->make(CommunityServices::class);

        if ( ! $make->exists($data['community_id']))
            throw  new ValidateException('内容不存在，可能已被删除了哦～');

        $data['pid'] = $replyId;
        if ($replyId) {
            $get = $this->dao->get($replyId);
            if (!$get) throw  new ValidateException('您回复的评论不存在');
            if ($get->pid) {
                $data['re_uid'] = $get->uid;
                $data['pid'] = $get->pid;
            }
        }

        $res = Db::transaction(function () use ($replyId, $data, $make) {
            $res = $this->dao->create($data);
            if ($replyId) $this->dao->incField($data['pid'], 'count_reply', 1);
            return $res;
        });

        return $this->dao->getWhere(['reply_id' => $res->reply_id], '*', [
            'author' => function ($query) {
                $query->field('uid,nickname,avatar');
            },
            'reply' => function ($query) {
                $query->field('uid,nickname,avatar');
            },
        ]);
    }

    /**
     * @param $id
     * @return void
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 19:26
     * @author: Trump
     */
    public function delete($id)
    {
        Db::transaction(function () use ($id) {
            $get = $this->dao->get($id);
            $make = app()->make(CommunityServices::class);
            if ($get && $get->pid) $this->dao->decField($get['pid'], 'count_reply', 1);
            $make->decField($get['community_id'], 'count_reply', 1);
            if ($get) $get->delete();
        });
    }

    /**
     * @param int $id
     * @param int $uid
     * @param int $status
     * @return void
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 19:44
     * @author: Trump
     */
    public function setStart(int $id, int  $uid, int $status)
    {
        $make = app()->make(RelevanceServices::class);
        $check  = $make->checkHas($uid, $id, RelevanceServices::TYPE_COMMUNITY_REPLY_START);
        if ($status) {
            if ($check)  throw new ValidateException('您已经赞过过他了～');
            $make->create($uid, $id, RelevanceServices::TYPE_COMMUNITY_REPLY_START, true);
            $this->dao->incField($id, 'count_start', 1);
        } else {
            if (!$check) throw new ValidateException('您还未赞过他哦～');
            $make->destory($uid, $id, RelevanceServices::TYPE_COMMUNITY_REPLY_START);
            $this->dao->decField($id, 'count_start', 1);
        }
    }

    /**
     * @param int $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 11:25
     * @author: Trump
     */
    public function statusForm(int $id): Form
    {
        $formData = $this->dao->get($id)->toArray();

        if ($formData['status'] !== 0) throw new ValidateException('请勿重复审核');

        $url = '/community/reply/status/'.$id;
        $form = Elm::createForm(Route::buildUrl($url)->build());

        $form->setRule([
            Elm::textarea('content', '评论内容')->disabled(true),

            Elm::radio('status', '审核状态', 1)->options([
                ['value' => -1, 'label' => '未通过'],
                ['value' => 1, 'label' => '通过']]
            )->control([
                ['value' => -1, 'rule' => [
                    Elm::textarea('refusal', '未通过原因', '')->required()
                ]]
            ]),
        ]);
        $formData['status'] = 1;
        return $form->setTitle('审核评论')->formData($formData);
    }
}
