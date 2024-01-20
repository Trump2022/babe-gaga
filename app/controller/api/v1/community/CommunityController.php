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

namespace app\controller\api\v1\community;

use app\Request;
use app\services\community\CommunityServices;
use app\services\kefu\ProductServices;
use app\services\product\product\StoreProductLogServices;
use app\services\system\config\SystemConfigServices;
//use app\common\repositories\store\order\StoreOrderProductRepository;
use app\services\system\RelevanceServices;
//use app\common\repositories\user\UserHistoryRepository;
//use app\common\repositories\user\UserRelationRepository;
use app\services\user\UserRelationServices;
use app\services\user\UserRelationsServices;
use app\services\user\UserHistoryServices;
use app\services\user\UserServices;
use app\services\store\order\StoreOrderProductServices;
use app\validate\api\CommunityValidate;
//use crmeb\services\MiniProgramService;
use crmeb\services\wechat\MiniProgram;
//use think\App;
use crmeb\services\SystemConfigService;
use think\exception\ValidateException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;
use think\response\Json;

/**
 * Class CommunityController
 * @packge app\controller\api\v1\community
 * @date : 2024/1/11 18:16
 * @author: QSW
 * @email: qsw2012@outlook.com
 */
class CommunityController
{
    protected ?int $uid = 0;
    protected ?object $user = NULL;
    protected ?CommunityServices $services = NULL;

    /**
     * @param Request $request
     * @param CommunityServices $services
     */
    public function __construct(Request $request, CommunityServices $services)
    {
        $this->services = $services;
        $this->uid = $request->uid() ?? 0;
        $this->user = $request->uid() ? $request->user() : null;
        if ( ! sys_config('community_status') ) throw  new ValidateException('未开启社区功能');
    }

    /**
     * 文章列表
     *
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function lst(Request $request): Response
    {
        $where = $request->getMore([
            ['keyword', ''],
            ['topic_id', ''],
            ['is_hot', ''],
            ['category_id', ''],
            ['spu_id', ''],
        ]);
        if ( ! $where['category_id']) {
            unset($where['category_id']);
        } else  if ($where['category_id'] == -1) {
            $where['is_type'] = CommunityServices::COMMUNIT_TYPE_VIDEO;
            unset($where['category_id']);
        }
        $where = array_merge($where,CommunityServices::IS_SHOW_WHERE);
        return app('json')->success($this->services->getApiList($where, $this->user));
    }

    /**
     * @param $request
     * @return array
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 15:32
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function checkParams($request): array
    {
        $data = $request->params([
            'image', 'topic_id', 'content', 'spu_id', 'order_id',
            ['is_type', 1],
            'video_link'
        ]);
        $config = system_config(["community_app_switch",'community_audit','community_video_audit']);
        $data['status'] = 0;
        $data['is_show'] = 0;
        if ($data['is_type'] == 1) {
            if (!in_array(CommunityServices::COMMUNIT_TYPE_FONT,$config['community_app_switch']))
                throw new ValidateException('社区图文未开启');
            if ($config['community_audit']) {
                $data['status'] = 1;
                $data['is_show'] = 1;
                $data['status_time'] = date('Y-m-d H:i:s', time());
            }
        } else {
            if ( ! in_array(CommunityServices::COMMUNIT_TYPE_VIDEO,$config['community_app_switch']))
                throw new ValidateException('短视频未开启');
            if ($config['community_video_audit']) {
                $data['status'] = 1;
                $data['is_show'] = 1;
                $data['status_time'] = date('Y-m-d H:i:s', time());
            }
            if (!$data['video_link']) throw new ValidateException('请上传视频');

        }

        $data['content'] = filter_emoji($data['content']);
        app()->make(CommunityValidate::class)->check($data);
        $arr = explode("\n", $data['content']);
        $title = rtrim(ltrim($arr[0]));
        if (mb_strlen($title) > 40 ){
            $data['title'] = mb_substr($title,0,30,'utf-8');
        } else {
            $data['title'] = $title;
        }
        if ($data['image']) $data['image'] = implode(',',$data['image']);
        return $data;
    }

    /**
     * @param $request
     * @return bool
     * @author Qinii
     * @day 10/30/21
     */
    public function checkUserAuth($request): bool
    {
        $user = $request->user();
        if ( sys_config('community_auth') ) {
            if ($user->phone) {
                return true;
            }
            throw  new ValidateException('请先绑定您的手机号');
        } else {
            return true;
        }
    }

    /**
     * 发布文章
     * @param Request $request
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function create(Request $request): Json
    {
        $data = $this->checkParams($request);
        $this->checkUserAuth($request);
        $data['uid'] = $request->uid();
        $res = $this->services->create($data);
        return app('json')->success(['community_id' => $res]);
    }

    /**
     * 编辑
     * @param Request $request
     * @param $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function update(Request $request, $id): Json
    {
        $data = $this->checkParams($request);
        $this->checkUserAuth($request);
        if( ! $this->services->uidExists($id, $this->user->uid)) return app('json')->success('内容不存在或不属于您');
        $this->services->edit($id, $data);
        return app('json')->success(['community_id' => $id]);
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function delete($id)
    {
        if ( ! $this->services->uidExists($id, $this->user->uid)) return app('json')->fail('内容不存在或不属于您');
        $this->services->destroy($id, $this->user);
        return app('json')->success('删除成功');
    }

    /**
     * 视频列表
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 2022/11/29
     */
    public  function videoShow(Request $request): Response
    {
        $where = CommunityServices::IS_SHOW_WHERE;
        $where['community_id'] = $request->param('id','');
        return app('json')->success($this->services->getApiVideoList($where, $this->user));
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 17:31
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function show($id): Response
    {
        return app('json')->success('ok', $this->services->show($id, $this->user));
    }

    /**
     * 关注的人的文章
     *
     * @param Request $request
     * @param RelevanceServices $RelevanceServices
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 11/2/21
     */
    public function focusList(Request $request, RelevanceServices $RelevanceServices): Response
    {
        $where = CommunityServices::IS_SHOW_WHERE;
        $where_ = [
            'left_id' => $this->user->uid ?? null ,
            'type'    => RelevanceServices::TYPE_COMMUNITY_FANS,
        ];
        $where['uids'] = $RelevanceServices->getSearch($where_)->column('right_id');
        $type = $request->param('type',0);
        if ($type) {
            $where['is_type'] = CommunityServices::COMMUNIT_TYPE_VIDEO;
        }
        return app('json')->success($this->services->getApiList($where, $this->user));
    }

    /**
     * 某个用户的文章
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function userCommunityList($id): Response
    {
        $where = [];
        if ( ! $this->user || $this->user->uid !=  $id) {
            $where = CommunityServices::IS_SHOW_WHERE;
        }
        $where['uid'] = $id;
        return app('json')->success($this->services->getApiList($where, $this->user));
    }

    /**
     * 某个用户的视频
     *
     * @param Request $request
     * @param mixed $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function userCommunityVideoList(Request $request, $id = 0): Json
    {
        $where = [];
        $is_start = $request->param('is_star',0);
        if ($is_start) {
            //某人赞过的视频
            $where = CommunityServices::IS_SHOW_WHERE;
        } else {
            //某个人的视频
            if (!$this->user || $this->user->uid !=  $id) {
                $where = CommunityServices::IS_SHOW_WHERE;
            }
            $where['uid'] = $id;
        }
        $where['is_del'] = 0;
        $where['community_id'] = $request->param('community_id','');

        $data = $this->services->getApiVideoList($where, $this->user, (int) $is_start);
        return app('json')->success($data);
    }

    /**
     * 收藏商品
     * @param Request $request
     * @return Json
     * @author Qinii
     * @day 10/28/21
     */
    public function relationList(Request $request): Json
    {
        $keyword = $request->param('keyword');
        $data = app()->make(UserRelationServices::class)->getUserProductToCommunity($keyword, $request->uid());
        return app('json')->success($data);
    }

    /**
     * @param Request $request
     * @param StoreProductLogServices $services
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:13
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function historyList(Request $request, StoreProductLogServices $services): Response
    {
        $where['keyword'] = $request->param('keyword');
        $where['uid'] = $request->uid();
        $where['type'] = 'visit';
        $result = $services->getListForCommunity($where);
        return app('json')->success($result);
    }

    /**
     * 我赞过的文章
     *
     * @param Request $request
     * @param RelevanceServices $RelevanceServices
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getUserStartCommunity(Request $request, RelevanceServices $RelevanceServices): Response
    {
        $where['uid'] = $request->uid();
        $data = $RelevanceServices->getUserStartCommunity($where);
        return app('json')->success($data);
    }

    /**
     * 文章点赞/取消
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:46
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function startCommunity(Request $request, $id): Response
    {
        $status = $request->param('status') == 1 ? 1 :0;
        if ( ! $this->services->exists($id)) return app('json')->fail('内容不存在');
        $this->services->setCommunityStart($id, $this->user, $status);
        if ($status) {
            return app('json')->success('点赞成功');
        } else {
            return app('json')->success('取消点赞');
        }
    }

    /**
     * 用户关注/取消
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @author Qinii
     * @day 10/28/21
     */
    public function setFocus(Request $request, $id): Response
    {
        $id  = (int)$id;
        $status  = $request->param('status') == 1 ? 1 :0;
        if ($this->user->uid == $id)
            return app('json')->fail('请勿关注自己');
        $make = app()->make(UserServices::class);
        if ( ! $make->exists($id)) return app('json')->fail('未查询到该用户');

        $this->services->setFocus($id, $this->user->uid, $status);

        if ($status) {
            return app('json')->success('关注成功');
        } else {
            return app('json')->success('取消关注');
        }
    }

    /**
     * 我的粉丝
     *
     * @param Request $request
     * @param RelevanceServices $RelevanceServices
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/28/21
     */
    public function getUserFans(Request $request, RelevanceServices $RelevanceServices): Json
    {
        $fans = $RelevanceServices->getUserFans($request->uid());
        return app('json')->success($fans);
    }

    /**
     * 我的关注
     * @param Request $request
     * @param RelevanceServices $RelevanceServices
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/28/21
     */
    public function getUserFocus(Request $request, RelevanceServices $RelevanceServices): Response
    {
        $start = $RelevanceServices->getUserFocus($request->uid());
        return app('json')->success($start);
    }

    /**
     * 用户信息
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/28/21
     */
    public function userInfo($id): Response
    {
        if ( ! $id)  return app('json')->fail('缺少参数');
        $data = $this->services->getUserInfo($id, $this->user);
        return app('json')->success($data);
    }

    /**
     * 已购商品
     * @param Request $request
     * @param ProductServices $ProductServices
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function payList(Request $request, ProductServices $ProductServices): Json
    {
        $keyword = $request->param('keyword') ?? '';
        return app('json')->success(get_thumb_water($ProductServices->getProductCartList((int) $request->uid(), $keyword)));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 18:02
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function qrcode(Request $request, $id): Response
    {
        $id = (int) $id;
        $type = $request->param('type');
        $url = $this->services->qrcode($id, $type, $request->user());
        if ( ! $url) return app('json')->fail('二维码生成失败');
        return app('json')->success(compact('url'));
    }

    /**
     * ToDo
     *
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:52
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getSpuByOrder($id): Response
    {
        $data = $this->services->getSpuByOrder($id);
        return app('json')->success($data);
    }
}
