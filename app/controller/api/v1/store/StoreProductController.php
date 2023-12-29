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
namespace app\controller\api\v1\store;

use app\Request;
use app\services\product\category\StoreProductCategoryServices;
use app\services\product\product\StoreProductServices;
use app\services\store\SystemStoreServices;

/**
 * 商品类
 * Class StoreProductController
 * @package app\controller\api\store
 */
class StoreProductController
{
    /**
     * 商品services
     * @var StoreProductServices
     */
    protected $services;

    public function __construct(StoreProductServices $services)
    {
        $this->services = $services;
    }

    /**
     * 商品列表
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request, StoreProductCategoryServices $services, SystemStoreServices $storeServices)
    {
        $where = $request->getMore([
            [['sid', 'd'], 0],
            [['cid', 'd'], 0],
            ['keyword', '', '', 'store_name'],
            ['priceOrder', ''],
            ['salesOrder', ''],
            [['news', 'd'], 0, '', 'is_new'],
            [['type', ''], '', '', 'status'],
            ['ids', ''],
            [['selectId', 'd'], ''],
            ['productId', ''],
            ['brand_id', ''],
            ['promotions_id', 0],
            ['store_id', 0, '', 'relation_id'],
            ['delivery_type', ''],
            [['collate_code_id', 'd'], 0], //拼单ID 、桌码ID
        ]);
        if ($where['selectId'] && (!$where['sid'] || !$where['cid'])) {
            if ($services->value(['id' => $where['selectId']], 'pid')) {
                $where['sid'] = $where['selectId'];
            } else {
                $where['cid'] = $where['selectId'];
            }
        }
		$where['ids'] = stringToArray($where['ids']);
        if (!$where['ids']) {
            unset($where['ids']);
        }
        if ($where['brand_id']) {
            $where['brand_id'] = explode(',', $where['brand_id']);
        }
        $type = 'mid';
        $field = ['image', 'recommend_image'];
        if ($where['store_name']) {
            $field = ['image'];
        }

		if ($where['relation_id']) {//获取定位门店商品
			if ($where['delivery_type'] == 2) {// 获取自提商品
				//验证平台自提开启，门店自提是否开启
				if(!sys_config('store_self_mention', 1) || !$storeServices->value(['id' => $where['relation_id'], 'is_show' => 1, 'is_del' => 0], 'is_store')) {
					return app('json')->success([]);
				}
			}
			$where['type'] = 1;
			$where['show_type'] = [0, 1];
			$list = $this->services->getGoodsList($where, (int)$request->uid());
        	return app('json')->successful(get_thumb_water($list, $type, $field));
		} else {
			return app('json')->success([]);
		}
    }

    /**
     * 搜索获取商品品牌列表
     * @param Request $request
     * @param StoreProductCategoryServices $services
     * @return mixed
     */
    public function brand(Request $request, StoreProductCategoryServices $services)
    {
		$where = $request->getMore([
			[['sid', 'd'], 0],
			[['cid', 'd'], 0],
			['store_id', 0, '', 'relation_id'],
			['selectId', '']
		]);
		if ($where['selectId'] && (!$where['sid'] || !$where['cid'])) {
			if ($services->value(['id' => $where['selectId']], 'pid')) {
				$where['sid'] = $where['selectId'];
			} else {
				$where['cid'] = $where['selectId'];
			}
		}
		$cate_id = [];
		if ($where['sid']) {
			$cate_id = [$where['sid']];
		} elseif ($where['cid']) {
			$cate_id = array_merge([$where['cid']], $services->getColumn(['pid' => $where['cid'], 'is_show' => 1], 'id'));
		}
		if ($where['relation_id']) {//获取定位门店商品
			$where['type'] = 1;
			return app('json')->successful($this->services->getBrandList(['cate_id' => $cate_id]));
		} else {
			return app('json')->success([]);
		}

    }


}
