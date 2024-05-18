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
namespace app\listener\order;

use app\services\order\StoreOrderCartInfoServices;
use app\jobs\order\OrderWriteoffJob;
use app\jobs\product\ProductLogJob;
use app\jobs\store\StoreFinanceJob;
use app\jobs\system\CapitalFlowJob;
use app\services\order\StoreOrderInvoiceServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderStatusServices;
use app\services\product\product\StoreProductServices;
use app\services\store\finance\StoreFinanceFlowServices;
use crmeb\interfaces\ListenerInterface;

/**
 * 订单核销事件
 * Class PriceRevision
 * @package app\listener\order
 */
class Writeoff implements ListenerInterface
{
    /**
     * @param $event
     */
    public function handle($event): void
    {
        [$orderInfo, $auth, $data, $cartIds, $cartInfo] = $event;
		$staff_id = $data['staff_id'] ?? 0;
		$oid = (int)$orderInfo['id'];

		//核销记录
		OrderWriteoffJob::dispatch([$oid, $cartIds, $data, $orderInfo]);

		/** @var StoreOrderStatusServices $statusServices */
		$statusServices = app()->make(StoreOrderStatusServices::class);
		if ($data['status'] == 5) {
			$message = [];
			foreach ($cartInfo as $item) {
				foreach ($cartIds as $value) {
					if ($value['cart_id'] === $item['cart_id']) {
						$message[] = '商品id:' . $item['product_id'] . ',核销数量:' . $value['cart_num'];
					}
				}
			}
			$statusServices->save([
				'oid' => $oid,
				'change_type' => 'writeoff',
				'change_message' => '订单部分核销，核销成员:' . $staff_id . ',核销商品:' . implode(' ', $message),
				'change_time' => time()
			]);
		} else {
			$statusServices->save([
				'oid' => $oid,
				'change_type' => 'writeoff',
				'change_message' => '订单核销已完成',
				'change_time' => time()
			]);
		}
		$message = [];
		/** @var StoreProductServices $productServices */
		// $productServices = app()->make(StoreProductServices::class);
		// foreach ($cartInfo as $item) {
		// 	foreach ($cartIds as $value) {
		// 		if ($value['cart_id'] === $item['cart_id']) {
		// 			$store_name = $productServices->value(['id'=>$item['product_id']],'store_name');
		// 			$message[] = substrUTf8($store_name, 10, 'UTF-8', '');
		// 		}
		// 	}
		// }
		// $data['store_name'] = implode(' ', $message);

        //获取购物车内的商品标题
        /** @var StoreOrderCartInfoServices $orderInfoServices */
        $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $storeName = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo['id']);
        $storeTitle = substrUTf8($storeName, 20, 'UTF-8', '');
        $storeTitle = $storeTitle ?? '';
        $data['store_name'] = $storeTitle;

		$data['phone'] = $orderInfo['user_phone'];
		$data['uid'] = $orderInfo['uid'];
		event('notice.notice', [$data, 'reminder_verification_status']);

		if ($auth == 1 && $data['staff_id']) {
			//流水关联店员
			/** @var StoreFinanceFlowServices $storeFinanceFlow */
			$storeFinanceFlow = app()->make(StoreFinanceFlowServices::class);
			$storeFinanceFlow->setStaff($orderInfo['order_id'], $data['staff_id']);
		}

    }
}
