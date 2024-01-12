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


namespace app\services\store\order;


use app\dao\store\order\StoreOrderProductDao;
use app\services\store\product\SpuServices;
use app\services\BaseServices;

/**
 * Class StoreOrderProductRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/8
 * @mixin StoreOrderProductDao
 */
class StoreOrderProductServices extends BaseServices
{
    /**
     * StoreOrderProductRepository constructor.
     * @param StoreOrderProductDao $dao
     */
    public function __construct(StoreOrderProductDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 已购商品
     *
     * @param string|null $keyword
     * @param int $uid
     * @return array
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:50
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getUserPayProduct(?string $keyword, int $uid): array
    {
        [$page, $limit] = $this->getPageValue();
        $query = $this->dao->getUserPayProduct($keyword, $uid)->group('product_id');
        $count = $query->count();
        $list = $query->setOption('field',[])->field('StoreOrderProduct.uid,StoreOrderProduct.product_id,StoreOrderProduct.product_type,spu_id,image,store_name,price')
            ->page($page, $limit)->select()->toArray();
       return compact('count', 'list');
    }
}
