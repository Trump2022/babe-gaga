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

namespace app\jobs\notice;


use app\services\activity\collage\UserCollageServices;
use app\services\activity\integral\StoreIntegralOrderServices;
use app\services\order\StoreOrderServices;
use crmeb\basic\BaseJobs;
use crmeb\services\printer\Printer;
use crmeb\traits\QueueTrait;
use think\facade\Log;


/**
 * 小票打印
 * Class PrintJob
 * @package app\jobs\notice
 */
class PrintJob extends BaseJobs
{
    use QueueTrait;

	/**
	 * 小票打印
	 * @param $name
	 * @param $configData
	 * @param $order
	 * @param $product
	 * @return bool|void
	 */
    public function doJob($id, $isTable = true)
    {
        try {
			if (!$id) {
				return true;
			}
			/** @var StoreOrderServices $orderServices */
			$orderServices = app()->make(StoreOrderServices::class);
			$orderServices->orderPrint((int)$id, -1, -1, !!$isTable);
        } catch (\Throwable $e) {
            Log::error('小票打印失败失败,失败原因:' . $e->getMessage());
        }
		return true;
    }

	/**
	 * 桌码流水小票打印
	 * @param $tableId
	 * @param $store_id
	 * @return bool
	 */
    public function tableDoJob($tableId, $store_id)
    {
        try {
			if (!(int)$tableId || !(int)$store_id) {
				return true;
			}
			/** @var UserCollageServices $collageServices */
            $collageServices = app()->make(UserCollageServices::class);
			$collageServices->tablePrint((int)$tableId, (int)$store_id);
        } catch (\Throwable $e) {
            Log::error('桌码流水小票打印失败失败,失败原因:' . $e->getMessage());
        }
		return true;
    }

	/**
	 * gaga币订单小票打印
	 * @param $id
	 * @return bool
	 */
	public function IntegralDoJob($id)
	{
		try {
			/** @var StoreIntegralOrderServices $storeIntegralOrderServices */
			$storeIntegralOrderServices = app()->make(StoreIntegralOrderServices::class);
			$storeIntegralOrderServices->orderPrint((int)$id);
		} catch (\Throwable $e) {
			Log::error('桌码流水小票打印失败失败,失败原因:' . $e->getMessage());
		}
		return true;
	}

}
