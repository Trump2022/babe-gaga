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
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\App;
use app\services\community\CommunityReplyServices;
use think\Response;

class CommunityReply extends AuthController
{
    /**
     * @param App $app
     * @param CommunityReplyServices $services
     */
    public function __construct(App $app, CommunityReplyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @return mixed
     * @author Qinii
     */
    public function lst(): Response
    {
        $where = $this->request->getMore([
            ['keyword', ''],
            ['date', ''],
            ['username', ''],
            ['community_id', 0],
            ['pid', 0],
        ]);
        return app('json')->success('ok', $this->services->getList($where));
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function delete($id): Response
    {
        if (!$this->services->get($id))  return app('json')->fail('数据不存在');
        $this->services->update($id, ['is_del' => 1]);
        return app('json')->success('删除成功');
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException|FormBuilderException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 11:20
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function statusForm($id): Response
    {
        if (!$this->services->get($id)) return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->services->statusForm($id)));
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 11:22
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function switchStatus($id): Response
    {
        $data = $this->request->getMore([
            ['status', 0],
            ['refusal', ''],
        ]);
        if ( ! in_array($data['status'], [1, -1]))  return app('json')->fail('审核类型错误');
        if ($data['status'] == -1 && empty($data['refusal'])) return app('json')->fail('请填写拒绝理由');
        if (!$this->services->get($id))  return app('json')->fail('数据不存在');
        $this->services->update($id, $data);
        return app('json')->success('审核成功');
    }
}
