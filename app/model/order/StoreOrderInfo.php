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

namespace app\model\order;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 *  订单记录Model
 * Class StoreOrderInfo
 * @package app\model\order
 */
class StoreOrderInfo extends BaseModel
{
    use ModelTrait;

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'store_order_info';

    /**
     * @var bool
     */
    protected $autoWriteTimestamp = false;

    /**
     * 更新时间获取器
     * @param $value
     * @return false|string
     */
    public function getUpdateTimeAttr($value)
    {
        if (!empty($value)) {
            return date('Y-m-d H:i:s', (int)$value);
        }
        return '';
    }

    /**
     * 订单ID搜索器
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOidAttr($query, $value, $data)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn('oid', $value);
            } else {
                $query->where('oid', $value);
            }
        }
    }

}
