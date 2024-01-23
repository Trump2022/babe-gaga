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

namespace app\services\order;


use app\dao\order\StoreOrderDao;
use app\dao\order\StoreOrderWriteoffDao;
use app\services\activity\integral\StoreIntegralOrderServices;
use app\services\activity\integral\StoreIntegralOrderStatusServices;
use app\services\activity\combination\StorePinkServices;
use app\services\BaseServices;
use app\services\pay\PayServices;
use app\services\store\SystemStoreServices;
use app\services\store\SystemStoreStaffServices;
use app\services\store\DeliveryServiceServices;
use app\services\supplier\SystemSupplierServices;
use app\services\user\UserServices;
use think\exception\ValidateException;

/**
 * 核销订单
 * Class StoreOrderWriteOffServices
 * @package app\sservices\order
 * @mixin StoreOrderDao
 */
class StoreOrderWriteOffServices extends BaseServices
{
	protected $orderDao;

	/**
	 * 构造方法
	 * @param StoreOrderWriteoffDao $dao
	 * @param StoreOrderDao $orderDao
	 */
    public function __construct(StoreOrderWriteoffDao $dao, StoreOrderDao $orderDao)
    {
		$this->dao = $dao;
        $this->orderDao = $orderDao;
    }

	/**
	 * 获取核销列表
	 * @param $where
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function writeOffList($where)
	{
		[$page, $limit] = $this->getPageValue();

		$list = $this->dao->getList($where, '*', $page, $limit, [
			'userInfo',
			'staffInfo',
			'orderInfo' => function ($query) {
						$query->field('id,order_id,pay_type');
					},
			'cartInfo' => function ($query) {
						$query->field('id,cart_info');
					},
		]);
		$count = $this->dao->count($where);
		if ($list) {
			$supplierIds = $storeIds = [];
			foreach ($list as $value) {
				switch ($value['type']) {
					case 0:
						break;
					case 1://门店
						$storeIds[] = $value['relation_id'];
						break;
					case 2://供应商
						$supplierIds[] = $value['relation_id'];
						break;
				}
			}
			$supplierIds = array_unique($supplierIds);
			$storeIds = array_unique($storeIds);
			$supplierList = $storeList = [];
			if ($supplierIds) {
				/** @var SystemSupplierServices $supplierServices */
				$supplierServices = app()->make(SystemSupplierServices::class);
				$supplierList = $supplierServices->getColumn([['id', 'in', $supplierIds], ['is_del', '=', 0]], 'id,supplier_name', 'id');
			}
			if ($storeIds) {
				/** @var SystemStoreServices $storeServices */
				$storeServices = app()->make(SystemStoreServices::class);
				$storeList = $storeServices->getColumn([['id', 'in', $storeIds], ['is_del', '=', 0]], 'id,name', 'id');
			}
			foreach ($list as &$item) {
				$cartInfo = $item['cartInfo'] ?? [];
				$cartInfo = is_string($cartInfo['cart_info']) ? json_decode($cartInfo['cart_info'], true) : $cartInfo['cart_info'];
				$item['productInfo'] = $cartInfo['productInfo'] ?? [];
				$orderInfo = $item['orderInfo'] ?? [];
				$item['order_id'] = $orderInfo['order_id'] ?? '';
				$item['pay_type'] = $orderInfo['pay_type'] ?? '';
				switch ($item['pay_type']) {
					case PayServices::WEIXIN_PAY:
						$item['pay_type_name'] = '微信支付';
						break;
					case PayServices::YUE_PAY:
						$item['pay_type_name'] = '余额支付';
						break;
					case PayServices::OFFLINE_PAY:
						$item['pay_type_name'] = '线下支付';
						break;
					case PayServices::ALIAPY_PAY:
						$item['pay_type_name'] = '支付宝支付';
						break;
					case PayServices::CASH_PAY:
						$item['pay_type_name'] = '现金支付';
						break;
					default:
						$item['pay_type_name'] = '其他支付';
						break;
				}
				$item['plate_name'] = '平台';
				switch ($item['type']) {
					case 0:
						$item['plate_name'] = '平台';
						break;
					case 1://门店
						$item['plate_name'] = '门店：' . ($storeList[$item['relation_id']]['name'] ?? '');
						break;
					case 2://供应商
						$item['plate_name'] = '供应商：' . ($supplierList[$item['relation_id']]['supplier_name'] ?? '');
						break;
				}
			}
		}

		return compact('list', 'count');
	}

	/**
	 * 保存核销记录
	 * @param int $oid
	 * @param array $cartIds
	 * @param array $data
	 * @param array $orderInfo
	 * @param array $cartInfo
	 * @return bool
	 */
	public function saveWriteOff(int $oid, array $cartIds = [], array $data = [], array $orderInfo = [], array $cartInfo = [])
	{
		if (!$oid) {
			throw new ValidateException('缺少核销订单信息');
		}
		if (!$orderInfo) {
			/** @var StoreOrderServices $storeOrderServices */
			$storeOrderServices = app()->make(StoreOrderServices::class);
			$orderInfo = $storeOrderServices->get($oid);
		}
		if (!$orderInfo) {
			throw new ValidateException('核销订单不存在');
		}
		$orderInfo = is_object($orderInfo) ? $orderInfo->toArray() : $orderInfo;
		if (!$cartInfo) {
			/** @var StoreOrderCartInfoServices $cartInfoServices */
			$cartInfoServices = app()->make(StoreOrderCartInfoServices::class);
			if ($cartIds) {//商城存在部分核销
				$ids = array_unique(array_column($cartIds, 'cart_id'));
				$cartIds = array_combine($ids, $cartIds);
				//订单下原商品信息
				$cartInfo = $cartInfoServices->getCartColunm(['oid' => $orderInfo['id'], 'cart_id' => $ids], '*', 'cart_id');
			} else {//整单核销
				$cartInfo = $cartInfoServices->getCartColunm(['oid' => $orderInfo['id']], '*', 'cart_id');
			}
		}

		$writeOffDataAll = [];
		$writeOffData = ['uid' => $orderInfo['uid'], 'oid' => $oid, 'writeoff_code' => $orderInfo['verify_code'], 'add_time' => time()];
		foreach ($cartInfo as $cart) {
			$write = $cartIds[$cart['cart_id']] ?? [];
			$info = is_string($cart['cart_info']) ? json_decode($cart['cart_info'], true) : $cart['cart_info'];
			if (!$cartIds || $write) {
				$writeOffData['order_cart_id'] = $cart['id'];
				$writeOffData['writeoff_num'] = $write['cart_num'] ?? $cart['cart_num'];
				$writeOffData['type'] = $cart['type'];
				$writeOffData['relation_id'] = $cart['relation_id'];
				$writeOffData['product_id'] = $cart['product_id'];
				$writeOffData['product_type'] = $cart['product_type'];
				$writeOffData['writeoff_price'] = (float)bcmul((string)$info['truePrice'], (string)$writeOffData['writeoff_num'], 2);
				$writeOffData['staff_id'] = $data['staff_id'] ?? 0;
				$writeOffData['store_id'] = $data['store_id'] ?? 0;
				$writeOffDataAll[] = $writeOffData;
			}
		}
		if ($writeOffDataAll) {
			$this->dao->saveAll($writeOffDataAll);
		}
		return true;
	}

    /**核销记录
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userOrderWriteOffRecords(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, '*', $page, $limit, ['cartInfo', 'systemStore']);
        $count = $this->dao->count($where);
        $times = [];
        if ($list) {
            foreach ($list as &$item) {
                $item['time_key'] = $item['time'] = $item['add_time'] ? date('Y-m-d H:i', (int)$item['add_time']) : '';
                $item['add_time'] = $item['add_time'] ? date('Y-m-d H:i', (int)$item['add_time']) : '';
                $value = is_string($item['cartInfo']['cart_info']) ? json_decode($item['cartInfo']['cart_info'], true) : $item['cartInfo']['cart_info'];
                $value['productInfo']['store_name'] = $value['productInfo']['store_name'] ?? "";
                $value['productInfo']['store_name'] = substrUTf8($value['productInfo']['store_name'], 10, 'UTF-8', '');
                $item['cartInfo'] = $value;
                if(empty($item['store_id'])){
                    $item['staff_name'] = '平台';
                    $item['store_name'] = '平台';
                }
            }
            $times = array_merge(array_unique(array_column($list, 'time_key')));
        }

        return ['count' => $count, 'list' => $list, 'time' => $times];
    }

}
