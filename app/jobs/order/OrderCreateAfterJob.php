<?php


namespace app\jobs\order;


use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderComputedServices;
use app\services\user\UserServices;
use crmeb\basic\BaseJobs;
use crmeb\services\CacheService;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * 订单创建
 * Class OrderCreateAfterJob
 * @package app\jobs
 */
class OrderCreateAfterJob extends BaseJobs
{
    use QueueTrait;

    /**
     * 清理订单确认生成缓存
     * @param int $uid
     * @param string $unique
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delOrderCache(int $uid, string $unique)
    {
        CacheService::redisHandler()->delete('user_order_' . $uid . $unique);
        return true;
    }

    /**
     * 删除购物车和更新用户收货地址
     * @return bool
     */
    public function delCartAndUpdateAddres($orderInfo, $group)
    {
        try {
            /** @var StoreOrderCreateServices $orderCreate */
            $orderCreate = app()->make(StoreOrderCreateServices::class);
            $orderCreate->orderCreateAfter($orderInfo, $group);
        } catch (\Throwable $e) {
            Log::error('更新用户信息和删除购物车失败,失败原因:' . $e->getMessage());
        }
        return true;
    }

    /**
     * 订单后置处理
     * @param $userInfo
     * @param $orderInfo
     * @param $data
     * @param $activity
     * @return bool
     */
    public function compute($userInfo, $orderInfo, $data, $activity)
    {
        if (!$userInfo || !$orderInfo || $userInfo['uid'] != $orderInfo['uid']) {
            return true;
        }
        $uid = (int)$orderInfo['uid'];
        $orderId = (int)$orderInfo['id'];
        try {
            $spread_uid = $spread_two_uid = 0;
            $cartInfo = $data['cartInfo'] ?? [];
            $priceData = $data['priceData'] ?? [];
            $addressId = $data['addressId'] ?? 0;

            /** @var StoreOrderCreateServices $createService */
            $createService = app()->make(StoreOrderCreateServices::class);
            $spread_ids = [];
            if ($cartInfo && $priceData) {
                /** @var StoreOrderCartInfoServices $cartServices */
                $cartServices = app()->make(StoreOrderCartInfoServices::class);
                [$cartInfo, $spread_ids] = $createService->computeOrderProductTruePrice($orderInfo, $cartInfo, $priceData, $addressId, $uid, $userInfo);
                $cartServices->updateCartInfo($orderId, $cartInfo);
            }

            $orderData = [];
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            // if ($spread_ids ) {
            //     [$spread_uid, $spread_two_uid] = $spread_ids;
            //     \think\facade\Log::write('AAATest|||aaa|||'.$spread_uid);
            // } else {
            //     $spread_uid = $userServices->getSpreadUid($uid);
            //     if ($spread_uid > 0 && sys_config('brokerage_level', 2) == 2) {
            //         $spread_two_uid = $userServices->getSpreadUid($spread_uid, [], false);
            //     }
            //     \think\facade\Log::write('AAATest|||bbb|||'.$spread_uid);
            // }
            if ($spread_ids ) {
                [$spread_uid, $spread_two_uid] = $spread_ids;
                if ( ! $spread_uid) {
                    $spread_uid = $userServices->getSpreadUid($uid);
                    if ($spread_uid > 0 && sys_config('brokerage_level', 2) == 2) {
                        $spread_two_uid = $userServices->getSpreadUid($spread_uid, [], false);
                    }
                }
            }
            \think\facade\Log::write('AAATest|||aaa|||'.$spread_uid.'|||'.$uid);

			if ($spread_uid > 0) {
				$orderData['spread_uid'] = $spread_uid;
			}
			if ($spread_two_uid > 0) {
				$orderData['spread_two_uid'] = $spread_two_uid;
			}
            if ($cartInfo && (isset($activity['type']) && !in_array($activity['type'], [1, 2, 3, 5, 8]))) {
                //gaga币与人民币折算比率, 积分抵用比例(1积分抵多少金额)单位：元
                $integral_ratio = sys_config('integral_ratio');
                $ratio = $integral_ratio > 0 ? $integral_ratio : 0.1;//必须大于0

                /** @var StoreOrderComputedServices $orderComputed */
                $orderComputed = app()->make(StoreOrderComputedServices::class);
                if ($userServices->checkUserPromoter($spread_uid)) {
                    $orderData['one_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'one_brokerage', false);
                    $orderData['one_brokerage_gaga'] = round( $orderData['one_brokerage'] / $ratio, 1);//佣金RMB对应的gaga币数量
                }
                if ($userServices->checkUserPromoter($spread_two_uid)) {
                    $orderData['two_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'two_brokerage', false);
                    $orderData['two_brokerage_gaga'] = round( $orderData['two_brokerage'] / $ratio, 1);//佣金RMB对应的gaga币数量
                }
            }
            if ($orderData) $createService->update(['id' => $orderId], $orderData);
        } catch (\Throwable $e) {
            Log::error('计算订单实际优惠、gaga币、邮费、佣金失败，原因：' . $e->getMessage());
        }

        return true;
    }
}
