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
namespace app\controller\api\v1\activity;

use app\Request;
use app\services\activity\ActivityServices;

/**
 * 定制活动类
 * Class ActivityController
 * @package app\controller\api\publics
 */
class ActivityController
{
    protected $services;

    public function __construct(ActivityServices $services)
    {
        $this->services = $services;
    }

    /**
     * 定制活动列表
     * @param Request $request
     * @param $cid
     * @return mixed
     */
    public function lst($cid)
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->cidByActivityList(['cid' => $cid], $page, $limit, "id,title,image_input,visit,from_unixtime(add_time,'%Y-%m-%d %H:%i') as add_time,synopsis,url");
        return app('json')->successful($list);
    }

    /**
     * 定制活动详情
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details($id)
    {
        $info = $this->services->getInfo($id);
        return app('json')->successful($info);
    }

    /**
     * 获取热门定制活动
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hot()
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->cidByActivityList(['is_hot' => 1], $page, $limit, "id,title,image_input,visit,from_unixtime(add_time,'%Y-%m-%d %H:%i') as add_time,synopsis,url");
        return app('json')->successful($list);
    }

    /**
     * 获取最新定制活动
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function new()
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->cidByActivityList([], $page, $limit, "id,title,image_input,visit,from_unixtime(add_time,'%Y-%m-%d %H:%i') as add_time,synopsis,url");
        return app('json')->successful($list);
    }

    /**
     * 获取顶部banner定制活动
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function banner()
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->cidByActivityList(['is_banner' => 1], $page, $limit, "id,title,image_input,visit,from_unixtime(add_time,'%Y-%m-%d %H:%i') as add_time,synopsis,url");
        return app('json')->successful($list);
    }
}
