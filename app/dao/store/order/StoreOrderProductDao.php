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


namespace app\dao\store\order;


use app\dao\BaseDao;
use app\model\store\order\StoreOrderProduct;
use think\Collection;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Model;
use think\model\Relation;

/**
 * Class StoreOrderProductDao
 * @package app\common\dao\store\order
 * @author xaboy
 * @day 2020/6/10
 */
class StoreOrderProductDao extends BaseDao
{
    const ORDER_VERIFY_STATUS_ = 1;
    const ORDER_VERIFY_STATUS_SUCCESS = 3;
    /**
     * @return string
     * @author xaboy
     * @day 2020/6/10
     */
    protected function setModel(): string
    {
        return StoreOrderProduct::class;
    }

    /**
     * @param $id
     * @param $uid
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function userOrderProduct($id, $uid)
    {
        return StoreOrderProduct::getDB()->where('uid', $uid)->where('order_product_id', $id)->with(['orderInfo' => function (Relation $query) {
            $query->field('order_id,order_sn,mer_id')->where('status', 2);
        }])->find();
    }

    /**
     * @param $orderId
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/6/12
     */
    public function noReplyProductCount($orderId): int
    {
        return StoreOrderProduct::getDB()->where('order_id', $orderId)->where('is_refund','<>','3')->where('is_reply', 0)
            ->count();
    }

    /**
     * @param array $ids
     * @param $uid
     * @param null $orderId
     * @param int $refund_switch
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/12
     */
    public function userRefundProducts(array $ids, $uid, $orderId = null, int $refund_switch = 1): Collection
    {
        return StoreOrderProduct::getDB()
            ->when($ids,function($query) use($ids){
                $query->whereIn('order_product_id', $ids);
            })
            ->when($orderId, function ($query) use($orderId) {
                 $query->where('order_id', $orderId);
            })
            ->when($uid, function ($query) use($uid) {
                 $query->where('uid', $uid);
            })
            ->when($refund_switch, function ($query) use($refund_switch) {
                $query->where('refund_switch', $refund_switch);
            })
            ->where('refund_num', '>', 0)->select();
    }

    /**
     * @param $date
     * @param $merId
     * @param int $limit
     * @return array
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:48
     * @author: Trump
     */
    public function orderProductGroup($date, $merId = null, int $limit = 7): array
    {
        return StoreOrderProduct::getDB()->alias('A')->leftJoin('StoreOrder B', 'A.order_id = B.order_id')
            ->field(Db::raw('sum(A.product_num) as total,A.product_id,cart_info'))
            ->withAttr('cart_info', function ($val) {
                return json_decode($val, true);
            })->when($date, function ($query, $date) {
                getModelTime($query, $date, 'B.pay_time');
            })->when($merId, function ($query, $merId) {
                $query->where('B.mer_id', $merId);
            })->where('B.paid', 1)->group('A.product_id')->limit($limit)->order('total DESC')->select();
    }

    /**
     * @param $date
     * @return mixed
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:48
     * @author: Trump
     */
    public function dateProductNum($date)
    {
        return StoreOrderProduct::getDB()->alias('A')->leftJoin('StoreOrder B', 'A.order_id = B.order_id')->when($date, function ($query, $date) {
            getModelTime($query, $date, 'B.pay_time');
        })->where('B.paid',1)->sum('A.product_num');
    }

    /**
     * 用户购买活动商品数量
     * @param int $activityId
     * @param int $uid
     * @param int $productType
     * @return int
     * @throws DbException
     * @author Qinii
     * @day 2020-10-23
     */
    public function getUserPayCount(int $activityId,int $uid,int $productType): int
    {
        $query = StoreOrderProduct::hasWhere('orderInfo',function($query){
            //  已支付/未支付
            $query->where('is_del',0)->whereOr(function($query){
                $query->where('paid',1)->where('is_del',1);
            });
        });
        $query->where('uid',$uid)->where('product_type',$productType)->where('activity_id',$activityId);
        $count = $query->count();
        return $count;
    }

    /**
     * @param string|null $keyword
     * @param int $uid
     * @return BaseQuery
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:48
     * @author: Trump
     */

    public function getUserPayProduct(?string  $keyword, int $uid): BaseQuery
    {
        $query = StoreOrderProduct::hasWhere('spu',function($query) use($keyword){
            $query->when($keyword, function ($query) use($keyword) {
               $query->whereLike('store_name',"%{$keyword}%");
            });
            $query->where('product_type',0);
        });
        $query->where('uid', $uid)->where('StoreOrderProduct.product_type',0);
        return  $query;
    }
}
