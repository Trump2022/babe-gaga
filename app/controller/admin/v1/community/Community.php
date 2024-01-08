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
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\App;
use app\services\community\CommunityServices;
use think\Response;

/**
 * Class Community
 * @packge app\controller\admin\v1\community
 * @date : 2024/1/8 14:56
 * @author: QSW
 * @email: qsw2012@outlook.com
 */
class Community extends AuthController
{
    /**
     * @param App $app
     * @param CommunityServices $services
     */
    public function __construct(App $app, CommunityServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    public function title()
    {
        $where['is_del'] =  0;
        return app('json')->success('ok', $this->services->title($where));
    }

    /**
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 15:12
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function lst(): Response
    {
        $where = $this->request->getMore([
            ['keyword', ''],
            ['status', ''],
            ['username', ''],
            ['category_id', 0],
            ['topic_id', 0],
            ['is_show', ''],
            ['is_type', ''],
        ]);
        $where['order'] =  'start';
        $where['is_del'] =  0;
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
    public function detail($id): Response
    {
        if ( ! $this->services->get($id)) return app('json')->fail('数据不存在');
        return app('json')->success('ok', $this->services->detail($id));
    }

    public function updateForm($id)
    {
        return app('json')->success(formToData($this->services->form($id)));
    }

    public function showForm($id)
    {
        return app('json')->success(formToData($this->services->showForm($id)));
    }

    public function update($id)
    {
        $data['start']  = $this->request->param('start',1);
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');

        $this->services->update($id,$data);
        return app('json')->success('修改成功');
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
        $this->services->destory($id);
        return app('json')->success('删除成功');
    }

    public function switchStatus($id)
    {
        $data = $this->request->params(['status', 'refusal']);

        if (!in_array($data['status'],[0,1,-1,-2]))
            return app('json')->fail('状态类型错误');

        $data['is_show'] = ($data['status'] == 1) ? : 0;

        if($data['status'] == -1 && empty($data['refusal']))
            return app('json')->fail('请填写拒绝理由');
        if($data['status'] == -2 && empty($data['refusal']))
            return app('json')->fail('请填写下架原因');
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');

        $this->services->setStatus($id,$data);
        return app('json')->success('操作成功');
    }

    public function switchShow($id)
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');

        $this->services->update($id,['is_show' => $status]);
        return app('json')->success('修改成功');
    }

}
