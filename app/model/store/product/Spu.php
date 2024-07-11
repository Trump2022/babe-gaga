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

namespace app\model\store\product;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Class Spu
 * @packge app\model\store\product
 * @date : 2024/1/9 16:08
 * @author: Trump
 */
class Spu extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'spu_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'store_spu';

    /**
     * @return string
     * @author Qinii
     * @day 12/18/20
     */
    public static function tablePk(): string
    {
        return 'spu_id';
    }

    /**
     * @return string
     * @author Qinii
     * @day 12/18/20
     */
    public static function tableName(): string
    {
        return 'store_spu';
    }

    /**
     * @param $value
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:36
     * @author: Trump
     */
    public function setMerLabelsAttr($value): string
    {
        if (!empty($value)) {
            if (!is_array($value))
                return ','. $value .',';
            return ','. implode(',', $value) .',';
        }
        return $value;
    }

    /**
     * @param $value
     * @return array|false|string[]
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:36
     * @author: Trump
     */
    public function getMerLabelsAttr($value)
    {
        if (!$value) return [];
        return explode(',',rtrim(ltrim($value,','),','));
    }

    /**
     * @param $value
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:36
     * @author: Trump
     */
    public function setSysLabelsAttr($value): string
    {
        if (!empty($value)) {
            if (!is_array($value))
                return ','. $value .',';
            return ','. implode(',', $value) .',';
        }
        return $value;
    }

    /**
     * @param $value
     * @return array|false|string[]
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:35
     * @author: Trump
     */
    public function getSysLabelsAttr($value)
    {
        if (!$value) return [];
        return explode(',',rtrim(ltrim($value,','),','));
    }

    /**
     * @param $value
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:35
     * @author: Trump
     */
    public function getImageAttr($value): string
    {
        if (is_int(strpos($value, 'http'))) {
            return $value;
        } else {
            return rtrim(sys_config('site_url'), '/') . $value;
        }
    }

    /**
     * @param $value
     * @return int
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:33
     * @author: Trump
     */
    public function getOtPriceAttr($value): int
    {
        return (int)$value;
    }
}