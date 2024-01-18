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
namespace app\controller\api\v1\user;

use app\Request;
use app\services\user\UserRelationServices;
use crmeb\services\CacheService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;


/**
 * 用户收藏
 * Class UserCollectController
 * @package app\controller\api\v1\user
 */
class UserCollectController
{
    protected $services = NUll;

    /**
     * UserCollectController constructor.
     * @param UserRelationServices $services
     */
    public function __construct(UserRelationServices $services)
    {
        $this->services = $services;
    }


    /**
     * 获取收藏列表
     *
     * @param Request $request
     * @return mixed
     */
    public function collect_user(Request $request)
    {
		[$category, $is_class] = $request->postMore([
            ['category', 'product'],
            ['is_class', 0]
        ], true);
        $uid = (int)$request->uid();
		$list = $this->services->getUserRelationList($uid, $is_class, $category);
		foreach ($list as &$item) {
		    $item['promotions'] = !isset($item['promotions']) || !$item['promotions'] ? (object)[] : $item['promotions'];
		}
		$count = $this->services->getUserCount($uid,0, UserRelationServices::TYPE_COLLECT, $category, $is_class);
        return app('json')->successful(compact('list', 'count'));
    }

    /**
     * 添加收藏
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function collect_add(Request $request): Response
    {
        [$id, $category] = $request->postMore([
            ['id', 0],
            ['category', 'product']
        ], true);
        if (!$id) return app('json')->fail('参数错误');
        if(is_numeric($id)) $id = [$id];
        $res = $this->services->productRelation($request->uid(), $id, 'collect', $category);
        if (!$res) {
            return app('json')->fail('添加收藏失败');
        } else {
            CacheService::clearTokenAll('relation_' . fmod((float)$request->uid(), (float)10));
            return app('json')->successful('收藏成功');
        }
    }

    /**
     * 取消收藏
     *
     * @param Request $request
     * @return mixed
     */
    public function collect_del(Request $request)
    {
        [$id, $category] = $request->postMore([
            ['id', 0],
            ['category', 'product']
        ], true);
        if (!$id) return app('json')->fail('参数错误');
        if (!is_array($id) && is_numeric($id)) $id = [$id];
        $uid = (int)$request->uid();
        $res = $this->services->unProductRelation($uid, $id, 'collect', $category);
        if (!$res) {
            return app('json')->fail('取消收藏失败');
        } else {
            CacheService::clearTokenAll('relation_' . fmod((float)$request->uid(), (float)10));
            return app('json')->successful('取消收藏成功');
        }
    }

    /**
     * 批量收藏
     * @param Request $request
     * @return mixed
     */
    public function collect_all(Request $request)
    {
        $collectInfo = $request->postMore([
            ['id', ''],
            ['category', 'product'],
        ]);
        $collectInfo['id'] = explode(',', $collectInfo['id']);
        if (!count($collectInfo['id'])) {
            return app('json')->fail('参数错误');
        }
        $uid = (int)$request->uid();
        $productIdS = $collectInfo['id'];
		if(is_numeric($productIdS)) $productIdS = [$productIdS];
        $res = $this->services->productRelation($uid, $productIdS, 'collect', $collectInfo['category']);
        if (!$res) {
            return app('json')->fail('收藏失败');
        } else {
            CacheService::clearTokenAll('relation_' . fmod((float)$request->uid(), (float)10));
            return app('json')->successful('收藏成功');
        }
    }
}
