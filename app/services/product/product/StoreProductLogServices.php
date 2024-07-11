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

namespace app\services\product\product;


use app\dao\product\product\StoreProductLogDao;
use app\services\order\StoreOrderCartInfoServices;
use app\services\BaseServices;
use app\services\product\branch\StoreBranchProductServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;

/**
 * 商品访问记录日志
 * Class StoreProductLogServices
 * @package app\services\product\product
 * @mixin StoreProductLogDao
 */
class StoreProductLogServices extends BaseServices
{
    /**
     * StoreProductLogServices constructor.
     * @param StoreProductLogDao $dao
     */
    public function __construct(StoreProductLogDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 创建各种访问日志
     * @param string $type
     * @param array $data
     * @return bool
     */
    public function createLog(string $type, array $data)
    {
        if (!in_array($type, ['order', 'pay', 'refund']) && (!isset($data['product_id']) || !$data['product_id'])) {
            throw new ValidateException('缺少商品ID');
        }
        $log_data = $log_data_all = [];
        $log_data['type'] = $type;
        $log_data['uid'] = $data['uid'] ?? 0;
        $log_data['add_time'] = time();
        switch ($type) {
            case 'visit'://访问
            case 'cart'://加入购物车
            case 'collect'://收藏
                $key = $type . '_num';
                $log_data[$key] = isset($data[$key]) && $data[$key] ? $data[$key] : 1;
                $log_data['product_id'] = 0;
                if (isset($data['product_id']) && $data['product_id']){
                    if (is_array($data['product_id'])) {
                        foreach ($data['product_id'] as $key => $product_id) {
                            $log_data['product_id'] = $product_id;
                            $log_data_all[] = $log_data;
                        }
                    } else {
                        $log_data['product_id'] = $data['product_id'];
						$log_data_all[] = $log_data;
                    }
                }
                break;
            case 'order'://下单
                if (!isset($data['order_id']) || !$data['order_id']) {
                    throw new ValidateException('缺少订单ID');
                }
                /** @var StoreOrderCartInfoServices $cartInfoServices */
                $cartInfoServices = app()->make(StoreOrderCartInfoServices::class);
                $cartInfo = $cartInfoServices->getOrderCartInfo((int)$data['order_id']);
                foreach ($cartInfo as $value) {
                    $product = $value['cart_info'];
                    $log_data['product_id'] = $product['product_id'] ?? 0;
                    $log_data['order_num'] = $product['cart_num'] ?? 1;
                    $log_data_all[] = $log_data;
                }
                break;
            case 'pay'://支付
                if (!isset($data['order_id']) || !$data['order_id']) {
                    throw new ValidateException('缺少订单ID');
                }
                /** @var StoreOrderCartInfoServices $cartInfoServices */
                $cartInfoServices = app()->make(StoreOrderCartInfoServices::class);
                $cartInfo = $cartInfoServices->getOrderCartInfo((int)$data['order_id']);
                foreach ($cartInfo as $value) {
                    $product = $value['cart_info'];
                    $log_data['product_id'] = $product['product_id'] ?? 0;
                    $log_data['pay_num'] = $product['cart_num'] ?? 0;
                    $log_data['cost_price'] = $product['costPrice'] ?? 0;
                    $log_data['pay_price'] = $product['truePrice'] ?? 0;
                    $log_data['pay_uid'] = $data['uid'] ?? 0;
                    $log_data_all[] = $log_data;
                }
                break;
            case 'refund'://退款
				if (!isset($data['order_id']) || !$data['order_id']) {
					throw new ValidateException('缺少订单ID');
				}
				/** @var StoreOrderCartInfoServices $cartInfoServices */
				$cartInfoServices = app()->make(StoreOrderCartInfoServices::class);
				$cartInfo = $cartInfoServices->getOrderCartInfo((int)$data['order_id']);
				foreach ($cartInfo as $value) {
					$product = $value['cart_info'];
					$log_data['product_id'] = $product['product_id'] ?? 0;
					$log_data['order_num'] = $product['cart_num'] ?? 1;
					$log_data_all[] = $log_data;
				}
                break;
            default:
                throw new ValidateException('暂不支持该类型记录');
                break;
        }
		$res = true;
        if ($log_data_all) {
			/** @var StoreBranchProductServices $productServices */
			$productServices = app()->make(StoreBranchProductServices::class);
			foreach ($log_data_all as &$item) {//平台共享到门店商品 记录平台商品ID
				$item['product_id'] = $productServices->getStoreProductId((int)$item['product_id']);
			}
            $res = $this->dao->saveAll($log_data_all);
        }
        if (!$res) {
            throw new ValidateException('添加商品记录失败');
        }
        return true;
    }

    /**
     * 查找购买商品排行
     * @param $where
     * @return mixed
     */
    public function getRanking(array $where)
    {
        $list = $this->dao->getRanking($where);
        foreach ($list as $key => &$item) {
			if (!$item['store_name'] || !$item['image']) {
				unset($list[$key]);
			}
            if ($item['profit'] == null) $item['profit'] = 0;
            if ($item['changes'] == null) $item['changes'] = 0;
            if ($item['repeats'] == null) {
                $item['repeats'] = 0;
            } else {
                $item['repeats'] = bcdiv($this->dao->getRepeats($where, $item['product_id']), $item['repeats'], 2);
            }
        }
        return array_merge($list);
    }

    /**
     * 浏览商品列表
     * @param array $where
     * @param string $group
     * @param string $field
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList(array $where, string $group = '', string $field = '*'): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $field, $page, $limit, $group);
        if ($group) {
            $count = $this->dao->getDistinctCount($where, $group, true);
        } else {
            $count = $this->dao->count($where);
        }
        return compact('list', 'count');
    }

    /**
     * @param array $where
     * @return array
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/18 14:50
     * @author: Trump
     */
    public function getListForCommunity(array $where): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->getQueryForHistoryList($where)
            ->when($page != 0 && $limit != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })
            ->field('r.product_id,r.product_id spu_id,r.uid,max(r.add_time) as add_time,p.product_type,p.store_name,p.image,p.price')
            ->group('r.product_id')
            ->order('add_time desc')
            ->select()->toArray();
        $count = $this->getQueryForHistoryList($where)->field('COUNT(distinct(r.product_id)) as count')->select()->toArray()[0]['count'] ?? 0;
        return compact('list', 'count');
    }

    /**
     * @param $where
     * @return mixed
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/18 14:49
     * @author: Trump
     */
    public function getQueryForHistoryList($where)
    {
        return $this->dao->getModel()->alias('r')
            ->join('store_product p', 'r.product_id=p.id')
            ->when(isset($where['keyword']) && $where['keyword'] !== '', function($q) use ($where){
                $q->whereLike('p.store_name',"%{$where['keyword']}%");
            })->where('r.uid',$where['uid'])
            ->where('r.type', $where['type']);
    }
}
