<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\controller\admin\v1\activity;

use app\controller\admin\AuthController;
use app\services\activity\ActivityServices;
use think\facade\App;

/**
 * 定制活动管理
 * Class Activity
 * @package app\controller\admin\v1\activity
 */
class Activity extends AuthController
{
    /**
     * @var ActivityServices
     */
    protected $service;

    /**
     * Activity constructor.
     * @param App $app
     * @param ActivityServices $service
     */
    public function __construct(App $app, ActivityServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 获取列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', ''],
            ['pid', 0, '', 'cid'],
        ]);
        $data = $this->service->getList($where);
        return $this->success($data);
    }

    /**
     * 保存定制活动数据
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['cid', ''],
            ['title', ''],
            ['author', ''],
            ['image_input', ''],
            ['synopsis', 0],
            ['share_title', ''],
            ['share_synopsis', ''],
            ['sort', 0],
            ['url', ''],
            ['is_banner', 0],
            ['is_hot', 0],
            ['status', 1]
        ]);
        $data['content'] = $this->request->param('content','');
        $this->service->save($data);
        return $this->success('添加成功!');
    }

    /**
     * 获取单个定制活动数据
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        if ($id) {
            $info = $this->service->read($id);
            return $this->success($info);
        } else {
            return $this->fail('参数错误');
        }

    }

    /**
     * 删除定制活动
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        if ($id) {
            $this->service->del($id);
            return $this->success('删除成功!');
        } else {
            return $this->fail('参数错误');
        }
    }

    /**
     * 定制活动关联商品
     * @param int $id
     * @return mixed
     */
    public function relation($id)
    {
        if (!$id) return $this->fail('缺少参数');
        [$product_id] = $this->request->postMore([
            ['product_id', 0]
        ], true);
        $res = $this->service->bindProduct($id, $product_id);
        if ($res) {
            return $this->success('关联成功');
        } else {
            return $this->fail('关联失败');
        }
    }

    /**
     * 取消商品关联
     * @param int $id
     * @return mixed
     */
    public function unrelation($id)
    {
        if (!$id) return $this->fail('缺少参数');
        $res = $this->service->bindProduct($id);
        if ($res) {
            return $this->success('取消关联成功！');
        } else {
            return $this->fail('取消失败');
        }
    }
}
