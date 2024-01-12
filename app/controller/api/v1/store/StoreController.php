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

use app\services\order\StoreCartServices;
use app\services\store\SystemStoreServices;
use app\services\store\SystemStoreStaffServices;
use app\Request;

/**
 * Class StoreController
 * @package app\controller\api\v1\store
 */
class StoreController
{
    protected $services;

    public function __construct(SystemStoreServices $services)
    {
        $this->services = $services;
    }

	/**
 	* 附近门店
	* @param Request $request
	* @param SystemStoreServices $services
	* @param StoreCartServices $cartServices
	* @return mixed
	* @throws \think\db\exception\DataNotFoundException
	* @throws \think\db\exception\DbException
	* @throws \think\db\exception\ModelNotFoundException
	 */
    public function nearbyStore(Request $request, SystemStoreServices $services, StoreCartServices $cartServices)
    {
        [$latitude, $longitude, $store_id] = $request->getMore([
            ['latitude', ''],
            ['longitude', ''],
            ['store_id', 0]//选择具体门店
        ], true);
		$where = [];
		if ((int)$store_id) {
			$where['id'] = $store_id;
		}
		$storeStatus = sys_config('store_func_status', 1);
		$storeInfo = [];
		//开启门店
		if ($storeStatus) {
			$storeInfo = $services->getNearbyStore($where, $latitude, $longitude, $request->ip(), 1);
		}
        $data['info'] = $storeInfo;
        $data['tengxun_map_key'] = sys_config('tengxun_map_key');
		$data['store_splicing_switch'] = $storeStatus && sys_config('store_splicing_switch');
		$uid = (int)$request->uid();
		$data['cart_num'] = 0;
		if ($uid && $data['info']) {
			$cartArr = $cartServices->getUserCartCount($uid, 0, (int)($data['info']['id'] ?? 0));
			$data['cart_num'] = $cartArr['count'] ?? 0;
		}
        return app('json')->successful($data);
    }

	/**
	* 获取门店列表
	* @param Request $request
	* @return mixed
	* @throws \think\db\exception\DataNotFoundException
	* @throws \think\db\exception\DbException
	* @throws \think\db\exception\ModelNotFoundException
	 */
	public function getStoreList(Request $request)
	{
		[$store_type, $keywords, $latitude, $longitude, $province, $city, $area, $street] = $request->getMore([
			['store_type', 1],
			['keyword', ''],
            ['latitude', ''],
            ['longitude', ''],
            ['province', 0],
            ['city', 0],
            ['area', 0],
            ['street', 0]
        ], true);
		$where = ['uid' => (int)$request->uid(), 'store_type' => $store_type, 'keywords' => $keywords, 'province' => $province, 'city' => $city, 'area' => $area, 'street' => $street];
		$storeList = [];
		//开启门店
		if (sys_config('store_func_status', 1)) {
			$storeList = $this->services->getNearbyStore($where, $latitude, $longitude);
		}
		return app('json')->success($storeList);
	}

    /**
     * 获取门店详情
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreInfo(Request $request)
    {
        $data = $request->getMore([
            ['id', 0]
        ]);
        $id = $data['id'];
        $storeInfo = $this->services->getStoreInfo($id);

        return app('json')->success($storeInfo);
    }

	/**
 	* 获取门店客服列表
	* @param SystemStoreStaffServices $staffServices
	* @param $store_id
	* @return mixed
	* @throws \think\db\exception\DataNotFoundException
	* @throws \think\db\exception\DbException
	* @throws \think\db\exception\ModelNotFoundException
	 */
	public function getCustomerList(SystemStoreStaffServices $staffServices, $store_id)
	{
		$customer = [];
		if ($store_id) {
			$customer = $staffServices->getCustomerList((int)$store_id, 'id,store_id,staff_name,avatar,customer_phone,customer_url');
		}
		return app('json')->success($customer);
	}

	/**
 	* 获取客服详情
	* @param SystemStoreStaffServices $staffServices
	* @param $id
	* @return mixed
	* @throws \think\db\exception\DataNotFoundException
	* @throws \think\db\exception\DbException
	* @throws \think\db\exception\ModelNotFoundException
	 */
	public function getCustomerInfo(SystemStoreStaffServices $staffServices, $id)
	{
		$info = [];
		if (!$id) {
			return app('json')->fail('缺少参数');
		}
		$info = $staffServices->getStaffInfo((int)$id, 'id,store_id,staff_name,avatar,customer_phone,customer_url');
		if (!$info) {
			return app('json')->fail('客服不存在');
		}
		$info = $info->toArray();
		$storeInfo = $this->services->getStoreInfo((int)$info['store_id']);
		$info['store_name'] = $storeInfo['name'] ?? '';
		$info['address'] = $storeInfo['address'] ?? '';
		$info['detailed_address'] = $storeInfo['detailed_address'] ?? '';
		$info['latitude'] = $storeInfo['latitude'] ?? '';
		$info['longitude'] = $storeInfo['longitude'] ?? '';
		$info['day_time'] = $storeInfo['day_time'] ?? '';
		$info['day_start'] = $storeInfo['day_start'] ?? '';
		$info['day_end'] = $storeInfo['day_end'] ?? '';
		return app('json')->success($info);
	}

}
