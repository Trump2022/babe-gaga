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
use app\validate\admin\CommunityTopicValidate;
use think\facade\App;
use app\services\community\CommunityTopicServices;

class CommunityTopic extends AuthController
{
    /**
     * @var CommunityTopicServices
     */
    protected $services;

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
        $where = $this->request->params(['topic_name', 'category_id', 'status','is_hot','is_del']);
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->services->getList($where, $page, $limit));
    }

    /**
     * TODO
     * @return \think\response\Json
     * @author Qinii
     * @day 10/26/21
     */
    public function createForm()
    {
        return app('json')->success(formToData($this->services->form(null)));
    }

    public function create()
    {
        $data = $this->checkParams();
        $data['topic_name'] = trim($data['topic_name']);
        if ($this->services->fieldExists('topic_name', $data['topic_name'],null))
            return app('json')->fail('话题重复');
        $this->services->create($data);
        app()->make(CommunityCategoryRepository::class)->clearCahe();
        return app('json')->success('添加成功');
    }

    public function updateForm($id)
    {
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->services->form($id)));
    }

    public function update($id)
    {
        $data = $this->checkParams();

        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');
        if ($this->services->fieldExists('topic_name', $data['topic_name'],$id))
            return app('json')->fail('话题重复');
        $this->services->update($id,$data);
        app()->make(CommunityCategoryRepository::class)->clearCahe();
        return app('json')->success('编辑成功');
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
        $this->services->update($id,['is_del' => 1]);
        app()->make(CommunityCategoryRepository::class)->clearCahe();
        return app('json')->success('删除成功');
    }

    public function switchStatus($id)
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');

        $this->services->update($id,['status' => $status]);
        app()->make(CommunityCategoryRepository::class)->clearCahe();
        return app('json')->success('修改成功');
    }

    public function checkParams()
    {
        $data = $this->request->params(['category_id','topic_name','is_hot','status','sort','pic']);
        app()->make(CommunityTopicValidate::class)->check($data);
        return $data;
    }

    public function getOptions()
    {
        return app('json')->success($this->services->options());
    }

    public function switchHot($id)
    {
        $status = $this->request->param('status', 0) == 1 ? 1 : 0;
        if (!$this->services->exists($id))
            return app('json')->fail('数据不存在');

        $this->services->update($id,['is_hot' => $status]);
        app()->make(CommunityCategoryRepository::class)->clearCahe();
        return app('json')->success('修改成功');
    }

}
