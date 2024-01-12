<?php

// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------


namespace app\model\store\order;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;
use app\model\community\Community;
use app\model\user\User;

class StoreOrder extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'order_id';
    }

    public static function tableName(): string
    {
        return 'store_order';
    }

    public function orderProduct()
    {
        return $this->hasMany(StoreOrderProduct::class, 'order_id', 'order_id');
    }

    public function refundProduct()
    {
        return $this->orderProduct()->where('refund_num', '>', 0);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    public function spread()
    {
        return $this->hasOne(User::class, 'uid', 'spread_uid');
    }

    public function TopSpread()
    {
        return $this->hasOne(User::class, 'uid', 'top_uid');
    }

    public function searchDataAttr($query, $value)
    {
        return getModelTime($query, $value);
    }

    // 核销订单的自订单列表
    public function takeOrderList()
    {
        return $this->hasMany(self::class,'main_id','order_id')->order('verify_time DESC');
    }

    public function searchMerIdAttr($query, $value)
    {
        return $query->where('mer_id', $value);
    }

    public function getRefundStatusAttr()
    {
        $day = (int) sys_config('sys_refund_timer') ?: 15;
        return ($this->verify_time ? strtotime($this->verify_time) > strtotime('-' . $day . ' day') : true);
    }

    public function getCancelTimeAttr()
    {
        if (!$this->apid) {
            $timer = ((int) sys_config('auto_close_order_timer')) ?: 15;
            return date('m-d H:i', strtotime("+ $timer minutes", strtotime($this->groupOrder->create_time)));
        }
        return null;
    }

    public function getOpenReceiptAttr()
    {
        return sys_config($this->mer_id,'mer_open_receipt') ?: 0;
    }

    public function getOrderExtendAttr($val)
    {
        return $val ? json_decode($val, true) : null;
    }

    public function getRefundExtensionOneAttr()
    {
        if ( $this->refundOrder ){
            return $this->refundOrder()->where('status',3)->sum('extension_one');
        }
        return 0;
    }

    public function getRefundExtensionTwoAttr()
    {
       if ( $this->refundOrder ){
           return $this->refundOrder()->where('status',3)->sum('extension_two');
       }
       return 0;
    }

    public function community()
    {
        return $this->hasOne(Community::class, 'order_id', 'order_id')->bind(['community_id']);
    }
}
