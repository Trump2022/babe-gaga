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

namespace app\controller\admin\v1\community;

use app\controller\admin\AuthController;
use think\facade\App;
use app\services\community\CommunityReplyServices;

class CommunityReply extends AuthController
{
    /**
     * @var CommunityReplyServices
     */
    protected $services;

    /**
     * User constructor.
     * @param App $app
     * @param  $services
     */
    public function __construct(App $app, CommunityReplyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * @return mixed
     * @author Qinii
     */
    public function lst()
    {
        $where = $this->request->params(['keyword', 'date', 'username', 'community_id', 'pid']);
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->services->getList($where, $page, $limit));
    }


    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function delete($id)
    {
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');
        $this->services->update($id, ['is_del' => 1]);
        return app('json')->success('删除成功');
    }

    public function statusForm($id)
    {
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->services->statusForm($id)));
    }


    public function switchStatus($id)
    {
        $data = $this->request->params(['status', 'refusal']);

        if (!in_array($data['status'], [1, -1]))
            return app('json')->fail('审核类型错误');

        if ($data['status'] == -1 && empty($data['refusal']))
            return app('json')->fail('请填写拒绝理由');

        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');

        $this->services->update($id, $data);
        return app('json')->success('审核成功');
    }
}
