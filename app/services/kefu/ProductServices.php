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

namespace app\services\kefu;


use app\services\BaseServices;
use app\services\product\product\StoreProductRelationServices;
use think\exception\ValidateException;
use app\dao\product\product\StoreProductDao;
use app\services\order\StoreOrderStoreOrderCartInfoServices;
use app\services\product\product\StoreProductVisitServices;

/**
 * Class ProductServices
 * @package app\services\kefu
 * @mixin StoreProductDao
 */
class ProductServices extends BaseServices
{
    const FMJUAVFD = 'WuJEUS';

    /**
     * ProductServices constructor.
     * @param StoreProductDao $dao
     */
    public function __construct(StoreProductDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取用户购买记录
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductCartList(int $uid, string $storeName = '')
    {
        [$page, $limit] = $this->getPageValue();
		$where = [];
		if (!$storeName) {//搜索不局限加入购物车｜浏览
			/** @var StoreOrderStoreOrderCartInfoServices $services */
			$services = app()->make(StoreOrderStoreOrderCartInfoServices::class);
			$where['id'] = $services->getUserCartProductIds(['uid' => $uid]);
		} else {
			$where['store_name'] = $storeName;
			$where['pid'] = 0;
		}
        $list = $this->dao->getProductCartList($where, $page, $limit, ['id', 'id product_id', 'id spu_id','product_type','IFNULL(sales,0) + IFNULL(ficti,0) as sales', 'store_name', 'image', 'stock', 'price']);
        $count = $this->dao->getProductCartCount($where);
        return compact('list', 'count');
    }

    /**
     * @param int $uid
     * @param string $storeName
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/26 9:35
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getProductCartCount(int $uid, string $storeName = '')
    {
        [$page, $limit] = $this->getPageValue();
        $where = [];
        if (!$storeName) {//搜索不局限加入购物车｜浏览
            /** @var StoreOrderStoreOrderCartInfoServices $services */
            $services = app()->make(StoreOrderStoreOrderCartInfoServices::class);
            $where['id'] = $services->getUserCartProductIds(['uid' => $uid]);
        } else {
            $where['store_name'] = $storeName;
            $where['pid'] = 0;
        }
        return $this->dao->getProductCartList($where, $page, $limit, ['id', 'id product_id', 'id spu_id','product_type','IFNULL(sales,0) + IFNULL(ficti,0) as sales', 'store_name', 'image', 'stock', 'price']);
    }

    /**
     * 获取用户浏览足记
     * @param int $uid
     * @return mixed
     */
    public function getVisitProductList(int $uid, string $storeName = '')
    {
        [$page, $limit] = $this->getPageValue();
		if ($storeName) {
			return $this->getProductCartList($uid, $storeName);
		} else {
			/** @var StoreProductVisitServices $service */
			$service = app()->make(StoreProductVisitServices::class);
			return $service->getUserVisitProductList(['uid' => $uid, 'store_name' => $storeName], $page, $limit);
		}

    }

    /**
     * 获取热销商品前20
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductHotSale(int $uid, string $storeName = '')
    {
		$where = [];
		$where['is_verify'] = 1;
		$where['is_show'] = 1;
		$where['is_del'] = 0;
		if (!$storeName) {//搜索不局限加入购物车｜浏览
			/** @var StoreOrderStoreOrderCartInfoServices $services */
			$services = app()->make(StoreOrderStoreOrderCartInfoServices::class);
			$productIds = $services->getUserCartProductIds(['uid' => $uid]);
			/** @var StoreProductRelationServices $cateService */
			$cateService = app()->make(StoreProductRelationServices::class);
			$where['id'] = $cateService->cateIdByProduct($cateService->productIdByCateId($productIds));
		} else {
			$where['store_name'] = $storeName;
			$where['pid'] = 0;
		}
        return $this->dao->getUserProductHotSale($where);
    }

    /**
     * 获取商品详情
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductInfo(int $id)
    {
        $productInfo = $this->dao->get($id, ['store_name', 'IFNULL(sales,0) + IFNULL(ficti,0) as sales', 'image',
            'slider_image', 'price', 'vip_price', 'ot_price', 'stock', 'id'], ['descriptions']);
        if (!$productInfo) {
            throw new ValidateException('商品未查到');
        }
        return $productInfo->toArray();
    }
}
