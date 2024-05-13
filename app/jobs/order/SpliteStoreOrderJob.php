<?php


namespace app\jobs\order;


use app\jobs\notice\PrintJob;
use app\jobs\store\StoreFinanceJob;
use app\services\order\StoreOrderCartInfoServices;
use app\services\store\StoreUserServices;
use app\webscoket\SocketPush;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * 门店分配订单
 * Class SpliteStoreOrderJob
 * @package app\jobs
 */
class SpliteStoreOrderJob extends BaseJobs
{
    use QueueTrait;

    /**
     * 订单分配完成后置方法
     * @param $orderInfo
     */
    public function splitAfter($orderInfo, bool $only_print = false)
    {
        //分配好向用户设置标签
        OrderJob::dispatchDo('setUserLabel', [$orderInfo]);
        //分配门店账单流水
        StoreFinanceJob::dispatch([$orderInfo, 1]);

        if ($only_print) {
			PrintJob::dispatch([(int)$orderInfo['id']]);
        } else {

            /*
            $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
            $storeName = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo['id']);
            $orderInfo['storeName'] = substrUTf8($storeName, 20, 'UTF-8', '');
            $orderInfo['storeName'] = $orderInfo['storeName'] ?? '';
            $orderInfo['send_name'] = $orderInfo['real_name'];

            //分配完成用户推送消息事件（门店小票打印）
            event('notice.notice', [$orderInfo, 'order_pay_success']);
            */
        }

        if (isset($orderInfo['store_id']) && $orderInfo['store_id']) {
            //记录门店用户
            /** @var StoreUserServices $storeUserServices */
            $storeUserServices = app()->make(StoreUserServices::class);
            $storeUserServices->setStoreUser((int)$orderInfo['uid'], (int)$orderInfo['store_id']);
        }
		event('notice.notice', [$orderInfo, 'admin_pay_success_code']);
        return true;
    }

}
