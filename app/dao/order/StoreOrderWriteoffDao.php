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

namespace app\dao\order;


use app\dao\BaseDao;
use app\model\order\StoreOrderWriteoff;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 订单核销
 * Class StoreOrderWriteoffDao
 * @package app\dao\order
 * @method saveAll(array $data)
 */
class StoreOrderWriteoffDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StoreOrderWriteoff::class;
    }

	/**
	 * @param array $where
	 * @return \crmeb\basic\BaseModel|mixed|\think\Model
	 */
	public function search(array $where = [])
	{
		return parent::search($where)
			->when(isset($where['keyword']) && $where['keyword'] , function($query) use ($where) {
				$query->where(function ($q) use ($where) {
					$q->whereLike('uid|oid|relation_id|staff_id|product_id', '%' . $where['keyword'] . '%')
						->whereOr('uid', 'IN', function ($user) use ($where) {
							$user->name('user')->field('id')->whereLike('uid|phone|nickname', '%' . $where['keyword'] . '%');
						})->whereOr('product_id', 'IN', function ($product) use ($where) {
							$product->name('store_product')->field('id')->whereLike('store_name|keyword', '%' . $where['keyword'] . '%');
						})->whereOr('oid', 'IN', function ($order) use ($where) {
							$order->name('store_order')->field('id')->whereLike('order_id|uid|user_phone|staff_id', '%' . $where['keyword'] . '%');
						});
				});
			});
	}

	/**
	 * 获取核销列表
	 * @param array $where
	 * @param string $field
	 * @param int $page
	 * @param int $limit
	 * @param array $with
	 * @return array
	 * @throws DataNotFoundException
	 * @throws DbException
	 * @throws ModelNotFoundException
	 */
	public function getList(array $where, string $field = '*', int $page = 0, int $limit = 0, array $with = []): array
    {
		return $this->search($where)->field($field)
			->when($with, function ($query) use ($with) {
				$query->with($with);
			})->when($page != 0 && $limit != 0, function ($query) use ($page, $limit) {
				$query->page($page, $limit);
			})->order('id desc')->select()->toArray();
	}
}
