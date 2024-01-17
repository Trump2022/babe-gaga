<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\services\order;

use app\dao\order\StoreOrderInfoDao;
use app\services\BaseServices;
use crmeb\traits\ServicesTrait;

/**
 * Class StoreOrderInfoServices
 * @package app\services\order
 * @mixin StoreOrderInfoDao
 */
class StoreOrderInfoServices extends BaseServices
{
    use ServicesTrait;

    /**
     * StorePinkServices constructor.
     * @param StoreOrderInfoDao $dao
     */
    public function __construct(StoreOrderInfoDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取课程订单的下单信息
     * @param int $oid
     * @return array|mixed
     */
    public function getOrderInfo(int $oid)
    {
        return $this->dao->getOrderInfo(['oid' => $oid]);
    }

    /**
     * 保存下单信息（课程订单）
     * @param int $oid
     * @param array $info
     * @return int|bool
     */
    public function saveOrderInfo(int $oid, array $info)
    {
        return $this->dao->saveOrderInfo($oid, $info);
    }

}