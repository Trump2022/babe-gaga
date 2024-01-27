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

use app\dao\community\CommunityDao;
use app\services\BaseServices;
//use app\services\user\UserBrokeragesServices;
use app\services\other\QrcodeServices;
use app\services\system\RelevanceServices;
use app\services\store\product\SpuServices;
use app\services\user\UserBillServices;
use app\services\user\UserServices;
use crmeb\basic\BaseModel;
use app\services\store\order\StoreOrderProductServices;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use app\model\product\product\StoreProduct;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;
use think\facade\Route;
use think\Model;

/**
 * Class CommunityServices
 * @packge app\services\community
 * @date : 2024/1/8 15:03
 * @author: QSW
 * @email: qsw2012@outlook.com
 * @mixin CommunityDao
 */
class CommunityServices extends BaseServices
{
    const IS_SHOW_WHERE = [
        'is_show' => 1,
        'status'  => 1,
        'is_del'  => 0,
    ];

    public const COMMUNIT_TYPE_FONT = '1';
    public const COMMUNIT_TYPE_VIDEO = '2';

    /**
     * CommunityRepository constructor.
     * @param CommunityDao $dao
     */
    public function __construct(CommunityDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $id
     * @param $data
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 15:25
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function setStatus($id, $data)
    {
        $ret = $this->dao->get($id);
        //event('community.status.before',compact('id','data'));
        Db::transaction(function () use($ret,$id, $data) {
            $data['status_time'] = date('Y-m-d H:i;s', time());
            $affected_rows = Db::name('community')
                ->where('community_id', $id)
                ->where('status', '<>', $data['status'])
                ->where('status', '=', $ret['status'])
                ->update($data);
            if ($affected_rows) {
                if ($data['status'] == 1) {
                    $this->incIntegral($ret['uid'], $id);
                } else if ($ret['status'] == 1){
                    $this->decIntegral($ret['uid'], $id);
                }
            }
            //event('community.status',compact('id'));
        });
    }

    /**
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 12:05
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getList(array $where = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $query = $this->dao->search($where)->with([
            'author' => function($query) {
                $query->field('uid,real_name,status,avatar,nickname,count_start');
            },
            'topic' => function($query) {
                $query->where('status', 1)->where('is_del',0);
                $query->field('topic_id,topic_name,status,category_id,pic,is_del');
            },
            'category'
        ]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count','list');
    }

    /**
     * @param $id
     * @param $user
     * @return void
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 15:47
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function destroy($id, $user = null)
    {
        //event('community.delete.before',compact('id','user'));
        $this->dao->update($id,  ['is_del' =>  1]);
        //event('community.delete',compact('id', 'user'));
    }

    /**
     * @param int $id
     * @return array|BaseModel|mixed|BaseQuery|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/28/21
     */
    public function detail(int $id)
    {
        $where = [
            $this->dao->getPk() => $id,
            'is_del' => 0
        ];
        return $this->dao->getSearch($where)->with([
            'author' => function($query) {
                $query->field('uid,real_name,status,avatar,nickname,count_start');
            },
            'topic',
            'category',
            'relevance.spu'
        ])->find();
    }

    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 12:01
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function showForm($id): Form
    {
        $url = '/community/community/status/'.$id;
        $form = Elm::createForm(Route::buildUrl($url)->build());
        $data = $this->dao->get($id);
        if (!$data) throw new ValidateException('数据不存在');
        return $form->setRule([
            Elm::hidden('status', -1),
            Elm::textarea('refusal', '下架理由', '信息存在违规')->required()
        ])->setTitle('强制下架');
    }

    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 12:00
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function form($id)
    {
        $url = '/community/community/update/'.$id;
        $form = Elm::createForm(Route::buildUrl($url)->build());
        $data = $this->dao->get($id);
        if (!$data) throw new ValidateException('数据不存在');
        $formData = $data->toArray();

        return $form->setRule([
            Elm::rate('start', '排序星级')->max(5)
        ])->setTitle('编辑星级')->formData($formData);
    }

    /**
     * @param array $where
     * @return array
     * @throws DbException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 15:34
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function title(array $where): array
    {
        $where['is_type'] = self::COMMUNIT_TYPE_FONT;
        $list[] = [
            'count' => $this->dao->search($where)->count(),
            'title' => '图文列表',
            'type' => self::COMMUNIT_TYPE_FONT,
        ];
        $where['is_type'] = self::COMMUNIT_TYPE_VIDEO;
        $list[] = [
            'count' => $this->dao->search($where)->count(),
            'title' => '短视频列表',
            'type' => self::COMMUNIT_TYPE_VIDEO,
        ];
        return  $list;
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param $userInfo
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 18:14
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getApiList(array $where, int $page, int $limit, $userInfo): array
    {
        $config = sys_config("community_app_switch");
        if (!isset($where['is_type']) && $config) $where['is_type'] = $config;
        $where['is_del'] = 0;
        $query = $this->dao->search($where)->order('start DESC,Community.create_time DESC,community_id DESC');
        $query->with([
            'author' => function($query) use($userInfo){
                $query->field('uid,real_name,status,avatar,nickname,count_start');
            },
            'is_start' => function($query) use ($userInfo) {
                $query->where('left_id',$userInfo->uid ?? null);
            },
            'topic' => function($query) {
                $query->where('status', 1)->where('is_del',0);
                $query->field('topic_id,topic_name,status,category_id,pic,is_del');
            },
            'relevance'  => [
                'spu' => function($query) {
                    $query->field('id,id product_id,id spu_id,store_name,image,price,product_type,0 as activity_id,is_class');
                }
            ],
            'is_fans' => function($query) use($userInfo){
                $query->where('left_id',$userInfo->uid??  0);
            }
        ]);
        $count = $query->count();
        $list = $query->page($page, $limit)->setOption('field',[])
            ->field('community_id,title,image,topic_id,Community.count_start,count_reply,start,Community.create_time,Community.uid,Community.status,is_show,content,video_link,is_type,refusal')
            ->select()->append(['time']);
        return compact('count','list');
    }

    public function getFirstVideo($where, $userInfo)
    {
        $where['is_del'] = 0;
//        $where['community_id'] = $community_id;
        $where['is_type'] = self::COMMUNIT_TYPE_VIDEO;
        $info = $this->dao->search($where)
            ->with([
                'author' => function ($query) use ($userInfo) {
                    $query->field('uid,real_name,status,avatar,nickname,count_start');
                },
                'is_start' => function ($query) use ($userInfo) {
                    $query->where('left_id', $userInfo->uid ?? null);
                },
                'topic' => function ($query) {
                    $query->where('status', 1)->where('is_del', 0);
                    $query->field('topic_id,topic_name,status,category_id,pic,is_del');
                },
                'relevance' => [
                    'spu' => function ($query) {
                        $query->field('id,id product_id,id spu_id,store_name,image,price,product_type,0 as activity_id,is_class');
                    }
                ],
                'is_fans' => function ($query) use ($userInfo) {
                    $query->where('left_id', $userInfo->uid ?? 0);
                }
            ])
            ->field('community_id,title,image,topic_id,Community.count_start,count_reply,start,Community.create_time,Community.uid,Community.status,is_show,content,video_link,is_type,refusal')
            ->find();
        if($info){
            $info = $info->append(['time']);
        }

        return $info;
    }

    /**
     * @param array $where
     * @param $userInfo
     * @param int $type
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 16:14
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getApiVideoList(array $where, $userInfo, int $type = 0): array
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_type'] = self::COMMUNIT_TYPE_VIDEO;

        if ($type) { // 点赞过的内容
            $where['uid'] = $userInfo->uid ?? 0;
            $where['community_ids'] = $this->dao->joinUser($where)->column('community_id');
            unset($where['uid']);
        }
        $first = $this->getFirstVideo($where,$userInfo);

        if ( ! $type) { // 条件视频
            if ( ! isset($where['uid']) && $first) $where['topic_id'] = $first['topic_id'];
        }

        if ($first) {
            $where['not_id'] = [$first['community_id']];
            $limit--;
        }

        unset($where['community_id']);
        $data = $this->getApiList($where, $page, $limit, $userInfo);
        if (empty($data['list']) && isset($where['topic_id'])) {
            unset($where['topic_id']);
            $data = $this->getApiList($where, $page, $limit, $userInfo);
        }

        if ($first) {
            $data['list']->unshift($first);
            $data['count']++;
        }
        return $data;
    }

    /**
     * 移动端详情展示
     *
     * @param int $id
     * @param $user
     * @return array|BaseModel|mixed|BaseQuery|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/27/21
     */
    public function show(int $id, $user)
    {
        $where = self::IS_SHOW_WHERE;
        $is_author = 0;
        if ($user && $this->dao->uidExists($id, $user->uid)) {
            $where = ['is_del' => 0];
            $is_author = 1;
        }
        $config = sys_config("community_app_switch");
        if ($config) $where['is_type'] = $config;
        $where[$this->dao->getPk()] = $id;
        $data = $this->dao->getSearch($where)
            ->with([
                'author' => function ($query) {
                    $query->field('uid,real_name,status,avatar,nickname,count_start,member_level');
                },
                 'relevance'=> [
                    'spu' => function ($query) {
                        $query->field('id,id product_id,id spu_id,store_name,image,price,product_type,0 as activity_id,is_class');
                    }
                ],
                'topic' => function ($query) {
                    $query->where('status', 1)->where('is_del', 0);
                    $query->field('topic_id,topic_name,status,category_id,pic,is_del');
                },
                'is_start' => function ($query) use ($user) {
                    $query->where('left_id', $user->uid ?? '');
                },
            ])->hidden(['is_del'])->find();

        if ( ! $data) throw new ValidateException('内容不存在，可能已被删除了哦～');
        $data = $data->toArray();

        $data['is_author'] = $is_author;
        $is_fans = 0;
        if ($user && !$data['is_author'])
            $is_fans = app()->make(RelevanceServices::class)->getWhereCount([
            'left_id' => $user->uid,
            'right_id' => $data['uid'],
            'type' => RelevanceServices::TYPE_COMMUNITY_FANS,
        ]);
        $data['is_fans'] = $is_fans;
        return $data;
    }

    /**
     * @param $id
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:52
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getSpuByOrder($id): array
    {
        $data = [];
        $where = app()->make(StoreOrderProductServices::class)->selectWhere(['order_id' => $id]);
        if (!$where) throw new  ValidateException('商品已下架');

        $make = app()->make(SpuServices::class);
        foreach ($where as $item) {
            switch ($item['product_type']){
                case 0:
                    $sid = $item['product_id'];
                   // nobreak;
                case 1:
                    $sid = $item['product_id'];
                    break;
                case 2:
                    $sid = $item['activity_id'];
                    break;
                case 3:
                    $sid = $item['cart_info']['productAssistSet']['product_assist_id'];
                    break;
                case 4:
                    $sid = $item['cart_info']['product']['productGroup']['product_group_id'];
                    break;
                default:
                    $sid = $item['product_id'];
                    break;

            }
            $data[] = $make->getSpuData($sid, $item['product_type'],0);
        }
        return $data;
    }

    /**
     * 创建
     * @param array $data
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function create(array $data)
    {
        //event('community.create.before',compact('data'));
        if ($data['topic_id']) {
            $getTopic  =  app()->make(CommunityTopicServices::class)->get($data['topic_id']);
            if (!$getTopic || !$getTopic->status) throw new ValidateException('话题不存在或已关闭');
            $data['category_id'] = $getTopic->category_id;
        }
        return Db::transaction(function () use($data) {
            $community = $this->dao->create($data);
            if ($data['status'] == 1) {
                $this->incIntegral($data['uid'], $community->community_id);
            }
            if ($data['spu_id']) $this->joinProduct($community->community_id,$data['spu_id']);
            //event('community.create',compact('community'));
            return $community->community_id;
        });
    }

    /**
     * @param $uid
     * @param $community_id
     * @return void
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/17 15:51
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function incIntegral($uid, $community_id)
    {
        $number = sys_config('member_community_num');
        if ($number) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userServices->incUpdate($uid, 'integral', $number);
            $userInfo = $userServices->getUserInfo($uid);

            /** @var UserBillServices $userBillServices */
            $userBillServices = app()->make(UserBillServices::class);
            $userBillServices->income('create_community_give_integral', $uid, (float) $number, $userInfo['integral'], $community_id);
        }
    }

    /**
     * @param $uid
     * @param $community_id
     * @return void
     */
    public function decIntegral($uid, $community_id)
    {
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $integralData = $userBillServices->getIncomeData('create_community_give_integral');
        $where = [
            'uid' => $uid,
            'link_id' => $community_id,
            'type' => $integralData['type'],
            'category' => $integralData['category'],
            'pm' => 1,
            'status' => 1
        ];
        $ub = $userBillServices->getLastGiveIntegral($where);
        if ($ub && $ub['number']) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userServices->decUpdate($uid, 'integral', $ub['number']);
            $userInfo = $userServices->getUserInfo($uid);
            $userBillServices->income('create_community_integral_refund', $uid, (float) $ub['number'], $userInfo['integral'], $community_id);
        }
    }

    /**
     * 编辑
     * @param int $id
     * @param array $data
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function edit(int $id, array $data)
    {
        //event('community.update.before',compact('id','data'));
        if ($data['topic_id']) {
            $getTopic  =  app()->make(CommunityTopicServices::class)->get($data['topic_id']);

            if (!$getTopic || !$getTopic->status) throw new ValidateException('话题不存在或已关闭');
            $data['category_id'] = $getTopic->category_id;
        }

        Db::transaction(function () use($id, $data) {
            $spuId = $data['spu_id'];
            unset($data['spu_id']);
            $community = $this->dao->update($id, $data);
            if ($spuId) $this->joinProduct($id, $spuId);
            //event('community.update.before',compact('id','community'));
        });
    }

    public function joinProduct($id, array $data)
    {
        $make = app()->make(RelevanceServices::class);
        $data = array_unique($data);
        $res = [];
        foreach ($data as $value) {
            if ($value) {
                $res[] = [
                    'left_id' => $id,
                    'right_id' => $value,
                    'type' => RelevanceServices::TYPE_COMMUNITY_PRODUCT
                ];
            }
        }
        $make->clear($id,RelevanceServices::TYPE_COMMUNITY_PRODUCT,'left_id');
        if($res) $make->saveAll($res);
    }

    /**
     * 获取某用户信息
     * @param int $uid
     * @param $self
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function getUserInfo(int $uid, $self = null): array
    {
        $RelevanceServices = app()->make(RelevanceServices::class);
        $data['focus'] = $RelevanceServices->getFieldCount('left_id', $uid,RelevanceServices::TYPE_COMMUNITY_FANS);


        $is_start = $is_self = false;
        if ($self && $self->uid == $uid) {
            $user = $self;
            $is_self = true;
        } else {
            $user = app()->make(UserServices::class)->get($uid);
            $is_start = $RelevanceServices->checkHas($self->uid, $uid, RelevanceServices::TYPE_COMMUNITY_FANS) > 0;
        }
        $data['start'] = $user->count_start;
        $data['uid']   = $user->uid;
        $data['avatar'] = $user->avatar;
        $data['nickname'] = $user->nickname;
        $data['is_start'] = $is_start;
        //$data['member_icon'] = sys_config('member_status') ? ($user->member->brokerage_icon ?? '')  : '';
        //$data['member_icon'] = sys_config('member_card_status') ? ($user->member->brokerage_icon ?? '')  : '';
        $data['is_self'] = $is_self;
        $data['fans'] = $user->count_fans;

        return $data;
    }

    /**
     * @param int $id
     * @param int $uid
     * @param int $status
     * @return void
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:54
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function setFocus(int $id, int  $uid,int $status)
    {
        $make = app()->make(RelevanceServices::class);
        $check  = $make->checkHas($uid, $id, RelevanceServices::TYPE_COMMUNITY_FANS);
        if ($status) {
            if ($check)  throw new ValidateException('您已经关注过他了～');
            $make->create($uid, $id,RelevanceServices::TYPE_COMMUNITY_FANS,true);
            app()->make(UserServices::class)->incField($id, 'count_fans', 1);
        } else {
            if (!$check) throw new ValidateException('您还未关注他哦～');
            $make->destory($uid, $id,RelevanceServices::TYPE_COMMUNITY_FANS);
            app()->make(UserServices::class)->decField($id, 'count_fans', 1);
        }
    }

    /**
     * @param int $id
     * @param $userInfo
     * @param int $status
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:54
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function setCommunityStart(int $id, $userInfo, int $status)
    {
        $make = app()->make(RelevanceServices::class);
        $userRepository = app()->make(UserServices::class);

        if ($status) {
            $res = $make->create($userInfo->uid, $id, RelevanceServices::TYPE_COMMUNITY_START,true);
            if (!$res) throw new ValidateException('您已经点赞过了');

            $ret = $this->dao->get($id);
            $user = $userRepository->get($ret['uid']);
            $this->dao->incField($id,'count_start',1);
            if ($user) $userRepository->incField((int)$user->uid,'count_start',1);
        }
        if (!$status) {
            if (!$make->checkHas($userInfo->uid, $id, RelevanceServices::TYPE_COMMUNITY_START))
                throw new ValidateException('您还没有点赞呢～');
            $make->destory($userInfo->uid, $id, RelevanceServices::TYPE_COMMUNITY_START);

            $ret = $this->dao->get($id);
            $user = $userRepository->get($ret['uid']);
            $this->dao->decField($id,'count_start',1);
            if ($user)  $userRepository->decField((int)$user->uid, 'count_start',1);
        }
    }

    public function getDataBySpu($spuId)
    {
        $where = array_merge(['spu_id' => $spuId], self::IS_SHOW_WHERE);
        $result = $this->dao->getSearch($where)
            ->field('community_id,title,image,is_type,create_time')
            ->order('create_time DESC')
            ->limit(3)->select();
        return $result;
    }

    /**
     * @param $id
     * @param $type
     * @param $user
     * @return bool|mixed|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 18:02
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function qrcode($id, $type,$user)
    {
        $res = $this->dao->search(['is_type' => self::COMMUNIT_TYPE_VIDEO,'community_id' => $id, 'status' => 1, 'is_show' => 1])->find();
        if (!$res) return false;

        /** @var QrcodeServices $qrcodeService */
        $qrcodeService = app()->make(QrcodeServices::class);

        if ($type == 'routine') {
            return $qrcodeService->getRoutineQrcodePath($id, $user['uid'], 11, []);
        } else {
            $name = md5('cwx' . $id . $type . $user->uid . $user['is_promoter'] . date('Ymd')) . '.jpg';
            $link = 'pages/short_video/nvueSwiper/index';
            $link = $link . '?id=' . $id . '&spid=' . $user['uid'];
            return $qrcodeService->getWechatQrcodePath($name, $link, false, false);
        }
    }
}

