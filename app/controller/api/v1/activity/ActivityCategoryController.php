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

use app\services\activity\ActivityCategoryServices;
use app\services\activity\ActivityServices;
use crmeb\services\CacheService;
use think\Request;

/**
 * 定制活动分类类
 * Class ActivityCategoryController
 * @package app\controller\api\publics
 */
class ActivityCategoryController
{
    protected $services;

    public function __construct(ActivityCategoryServices $services)
    {
        $this->services = $services;
    }
    /**
     * 定制活动分类列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lst(Request $request)
    {
        $where = $request->getMore([
            ['id', 0]
        ]);
        if(!empty($where['id'])){
            $cateInfo = $this->services->getActivityCategory($where);
        }else{
            $cateInfo = CacheService::get('ARTICLE_CATEGORY', function () {
                $cateInfo = $this->services->getActivityCategory();
                return $cateInfo;
            });
            if(!empty($cateInfo)){
                /** @var ActivityServices $activityServicess */
                $activityServicess = app()->make(ActivityServices::class);
                foreach ($cateInfo as &$itme){
                    $cid = $itme['id'];
                    $itme['activity_list'] = $activityServicess->cidByActivityList(['cid' => $cid], 1, 2, "id,title,image_input,visit,from_unixtime(add_time,'%Y-%m-%d %H:%i') as add_time,synopsis,url");
                }
            }
        }
        return app('json')->successful($cateInfo);
    }
}
