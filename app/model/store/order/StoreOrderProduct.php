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
use app\model\store\product\Product;
use app\model\store\product\Spu;
use app\model\user\User;
use app\model\store\order\StoreOrder;


class StoreOrderProduct extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'order_product_id';
    }

    public static function tableName(): string
    {
        return 'store_order_product';
    }

    public function getCartInfoAttr($value)
    {
        return json_decode($value, true);
    }

    public function orderInfo()
    {
        return $this->hasOne(StoreOrder::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'uid','uid');
    }

    public function product()
    {
        return $this->hasOne(Product::class,'product_id','product_id');
    }

    public function spu()
    {
        return $this->hasOne(Spu::class,'product_id','product_id');
    }

    public function searchUidAttr($query ,$value)
    {
        $query->where('uid',$value);
    }
    public function searchActivitIidAttr($query,$value)
    {
        $query->where('activity_id',$value);
    }
    public function searchProductTypeAttr($query,$value)
    {
        $query->where('product_type',$value);
    }
    public function searchOrderIdAttr($query,$value)
    {
        $query->where('order_id',$value);
    }
    public function searchProductIdAttr($query,$value)
    {
        $query->where('product_id',$value);
    }
    public function searchRefundSwitchAttr($query,$value)
    {
        $query->where('refund_switch',$value);
    }
    public function searchRefundNumAttr($query,$value)
    {
        $query->where('refund_num', '>' ,$value);
    }
}
