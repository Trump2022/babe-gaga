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
use app\services\community\CommunityCategoryServices;
use app\validate\admin\community\CommunityTopicValidate;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\App;
use app\services\community\CommunityTopicServices;
use think\Response;

/**
 * Class CommunityTopic
 * @packge app\controller\admin\v1\community
 * @date : 2024/1/9 16:38
 * @author: QSW
 * @email: qsw2012@outlook.com
 */
class CommunityTopic extends AuthController
{
    /**
     * @param App $app
     * @param CommunityTopicServices $services
     */
    public function __construct(App $app, CommunityTopicServices $services)
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
        $where = $this->request->getMore([
            ['topic_name', ''],
            ['category_id', ''],
            ['status', ''],
            ['is_hot', ''],
            ['is_del', ''],
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:09
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function detail($id): Response
    {
        $row = $this->services->get($id, ['*'], ['category']);
        if ( ! $row) return app('json')->fail('数据不存在');
        return app('json')->success('ok', $row->toArray());
    }

    /**
     * @return Response
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 10/26/21
     */
    public function createForm(): Response
    {
        return app('json')->success(formToData($this->services->form(null)));
    }

    /**
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 18:16
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function create(): Response
    {
        $data = $this->checkParams();
        $data['topic_name'] = trim($data['topic_name']);
        if ($this->services->fieldExists('topic_name', $data['topic_name'],null)) {
            return app('json')->fail('话题重复');
        }
        $this->services->create($data);
        return app('json')->success('添加成功');
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:01
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function updateForm($id): Response
    {
        if ( ! $this->services->get($id)) return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->services->form($id)));
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:03
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function update($id): Response
    {
        $data = $this->checkParams();
        if ( ! $this->services->get($id)) return app('json')->fail('数据不存在');
        if ($this->services->fieldExists('topic_name', $data['topic_name'],$id)) {
            return app('json')->fail('话题重复');
        }
        $this->services->update($id,$data);
        return app('json')->success('编辑成功');
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:18
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function delete($id): Response
    {
        if (!$this->services->get($id))  return app('json')->fail('数据不存在');
        $this->services->update($id,['is_del' => 1]);
        return app('json')->success('删除成功');
    }

    /**
     * @return array
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 18:15
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function checkParams(): array
    {
        $data = $this->request->postMore([
            ['category_id', 0],
            ['topic_name', ''],
            ['is_hot', 0],
            ['status', 0],
            ['sort', 0],
            ['pic', ''],
        ]);
        app()->make(CommunityTopicValidate::class)->check($data);
        return $data;
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:14
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function switchStatus($id): Response
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if ( ! $this->services->get($id)) return app('json')->fail('数据不存在');
        $this->services->update($id,['status' => $status]);
        return app('json')->success('修改成功');
    }

    /**
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:22
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getOptions(): Response
    {
        return app('json')->success($this->services->options());
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 19:22
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function switchHot($id): Response
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if (!$this->services->get($id)) return app('json')->fail('数据不存在');
        $this->services->update($id,['is_hot' => $status]);
        return app('json')->success('修改成功');
    }
}
