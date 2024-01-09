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

namespace app\services\store\product;

use app\common\repositories\store\product\ProductAssistRepository;
use app\common\repositories\store\product\ProductGroupRepository;
use app\common\repositories\store\product\ProductPresellRepository;
use app\common\repositories\store\StoreSeckillActiveRepository;
use app\dao\store\product\SpuDao;
use app\services\BaseServices;
use think\exception\ValidateException;

/**
 * Class SpuServices
 * @packge ${NAMESPACE}
 * @date : 2024/1/9 15:41
 * @author: QSW
 * @email: qsw2012@outlook.com
 * @mixin SpuDao
 */
class SpuServices extends BaseServices
{
    /**
     * @param SpuDao $dao
     */
    public function __construct(SpuDao $dao)
    {
        $this->dao = $dao;
    }


    public function getSpuData($id, $productType, $merId)
    {
        try {
            switch ($productType) {
                case 0:
                    $where = [
                        'activity_id' => 0,
                        'product_id' => $id,
                        'product_type' => $productType,
                    ];
                    break;
                case 1:
                    $_make = app()->make(StoreSeckillActiveRepository::class);
                    $res = $_make->getSearch(['product_id' => $id])->find();
                    $where = [
                        'activity_id' => $res['seckill_active_id'],
                        'product_id' => $id,
                        'product_type' => $productType,
                    ];
                    break;
                case 2:
                    $_make = app()->make(ProductPresellRepository::class);
                    $res = $_make->getWhere([$_make->getPk() => $id]);
                    $where = [
                        'activity_id' => $id,
                        'product_id' => $res['product_id'],
                        'product_type' => $productType,
                    ];
                    break;
                case 3:
                    $_make = app()->make(ProductAssistRepository::class);
                    $res = $_make->getWhere([$_make->getPk() => $id]);
                    $where = [
                        'activity_id' => $id,
                        'product_id' => $res['product_id'],
                        'product_type' => $productType,
                    ];
                    break;
                case 4:
                    $_make = app()->make(ProductGroupRepository::class);
                    $where[$_make->getPk()] = $id;
                    $res = $_make->getWhere([$_make->getPk() => $id]);
                    $where = [
                        'activity_id' => $id,
                        'product_id' => $res['product_id'],
                        'product_type' => $productType,
                    ];
                    break;
                default:
                    $where = [
                        'activity_id' => 0,
                        'product_id' => $id,
                        'product_type' => 0,
                    ];
                    break;
            }
        } catch (\Exception $e) {
            throw new ValidateException('数据不存在');
        }
        if ($merId) $where['mer_id'] = $merId;
        $result = $this->dao->getSearch($where)->find();
        if (!$result) throw new ValidateException('数据不存在');
        return $result;
    }
}