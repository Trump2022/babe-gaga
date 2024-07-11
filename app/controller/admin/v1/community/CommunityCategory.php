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
use app\validate\admin\StoreCategoryValidate;
use app\services\community\CommunityCategoryServices;
use think\Response;
use think\response\Json;

/**
 * Class CommunityCategory
 * @packge app\controller\admin\v1\community
 * @date : 2024/1/8 16:12
 * @author: Trump
 */
class CommunityCategory extends AuthController
{
    /**
     * @param App $app
     * @param CommunityCategoryServices $services
     */
    public function __construct(App $app, CommunityCategoryServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     */
    public function lst()
    {
        $where = $this->request->getMore([
            ['cate_name', ''],
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
     * @date : 2024/1/8 19:29
     * @author: Trump
     */
    public function detail($id): Response
    {
        $row = $this->services->get($id);
        if ( ! $row) return app('json')->fail('数据不存在');
        return app('json')->success($row->toArray());
    }

    /**
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 19:24
     * @author: Trump
     */
    public function createForm(): Json
    {
        return app('json')->success(formToData($this->services->form(null)));
    }

    /**
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 17:04
     * @author: Trump
     */
    public function create(): Response
    {
        $data = $this->checkParams();
        $data['cate_name'] = trim($data['cate_name']);
        if ($this->services->fieldExists('cate_name', $data['cate_name'],null)) {
            return app('json')->fail('分类名重复');
        }
        $this->services->create($data);
        return app('json')->success('添加成功');
    }

    /**
     * @return array
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 16:56
     * @author: Trump
     */
    public function checkParams(): array
    {
        $data = $this->request->getMore([
            ['pid', 0],
            ['cate_name', ''],
            ['is_show', 1],
            ['sort', 0],
        ]);
        validate(StoreCategoryValidate::class)->check($data);
        return $data;
    }

    /**
     * @param $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 17:51
     * @author: Trump
     */
    public function updateForm($id): Response
    {
        if ( ! $this->services->exists($id)) return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->services->form($id)));
    }

    /**
     * @param $id
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 17:52
     * @author: Trump
     */
    public function update($id): Response
    {
        $data = $this->checkParams();
        if ( ! $this->services->exists($id)) return app('json')->fail('数据不存在');
        if ($this->services->fieldExists('cate_name', $data['cate_name'],$id)) {
            return app('json')->fail('分类名重复');
        }
        $this->services->update($id,$data);
        return app('json')->success('编辑成功');
    }

    /**
     * @param $id
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 19:15
     * @author: Trump
     */
    public function delete($id): Response
    {
        if ( ! $this->services->exists($id)) return app('json')->fail('数据不存在');
        $this->services->delete($id);
        return app('json')->success('删除成功');
    }

    /**
     * @param $id
     * @return Response
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 19:26
     * @author: Trump
     */
    public function switchStatus($id): Response
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if ( ! $this->services->exists($id)) return app('json')->fail('数据不存在');

        $this->services->update($id,['is_show' => $status]);
        return app('json')->success('修改成功');
    }

    /**
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/11 17:27
     * @author: Trump
     */
    public function getOptions(): Response
    {
        return app('json')->success($this->services->options());
    }
}
