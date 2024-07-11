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
use app\services\system\RelevanceServices;
use app\services\community\CommunityReplyServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\Response;

/**
 * Class CommunityReplyController
 * @packge app\controller\api\v1\community
 * @date : 2024/1/11 18:16
 * @author: Trump
 */
class CommunityReplyController
{
    protected ?CommunityReplyServices $services = NULL;

    /**
     * @param CommunityReplyServices $services
     */
    public function __construct(CommunityReplyServices $services)
    {
        $this->services = $services;
        if ( ! sys_config('community_status')) throw  new ValidateException('未开启社区功能');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function lst(Request $request, $id): Response
    {
        if ( ! sys_config('community_reply_status'))
            return app('json')->success([
                'count' => 0,
                'all' => 0,
                'start' => 0,
                'list' => []
            ]);
        $where['community_id'] = $id;
        $userInfo = $request->uid() ? $request->user() : null;
        return app('json')->success($this->services->getApiList($where, $userInfo));
    }

    /**
     * 发评论
     * @param Request $request
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/29/21
     */
    public function create(Request $request, $id): Response
    {
        if ( ! sys_config('community_reply_status'))
            return app('json')->fail('评论功能未开启');
        if (sys_config('community_reply_auth') && ! $request->user()->phone)
            return app('json')->fail('请先绑定手机号');

        $replyId = $request->param('reply_id', 0);

        $data = $request->params(['content']);
        if (empty($data['content'])) return app('json')->fail('请输入回复内容');
        $data['uid'] = $request->uid();
        $data['community_id'] = $id;

        $data['status'] = 1;
        $msg = '回复成功';
        if (sys_config('community_reply_audit')) {
            $data['status'] = 0;
            $msg = '回复成功,正在审核中';
        }
        $ret = $this->services->create($replyId, $data);
        return app('json')->success($msg, $ret ? $ret->toArray() : []);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 19:26
     * @author: Trump
     */
    public function delete(Request $request, $id): Response
    {
        if ( ! $this->services->uidExists($id, $request->uid())) return app('json')->fail('评论不存在');
        $this->services->delete($id);
        return app('json')->success('评论删除');
    }

    /**
     * 评论点赞
     * @param Request $request
     * @param $id
     * @return Response
     * @author Qinii
     * @day 10/29/21
     */
    public function start(Request $request, $id): Response
    {
        if ( ! sys_config('community_reply_status')) return app('json')->success('评论不存在');
        $status = $request->param('status') == 1 ? 1 : 0;
        if (!$this->services->exists($id)) return app('json')->fail('评论不存在');

        $uid = $request->uid();
        $this->services->setStart($id, $uid, $status);
        if ($status) {
            return app('json')->success('点赞成功');
        } else {
            return app('json')->success('取消点赞');
        }
    }
}
