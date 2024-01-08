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
namespace app\model\activity;

use app\model\product\product\StoreProduct;
use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 *  定制活动Model
 * Class Activity
 * @package app\model\activity
 */
class Activity extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'activity';

    /**
     * 商品一对一关联
     * @return \think\model\relation\HasOne
     */
    public function storeInfo()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')
            ->field('store_name,image,price,id,ot_price');
    }

    /**
     * 定制活动详情一对一关联
     * @return \think\model\relation\HasOne
     */
    public function content()
    {
        return $this->hasOne(ActivityContent::class, 'nid', 'id')->bind(['content']);
    }

    /**
     * 定制活动详情一对一关联
     * @return \think\model\relation\HasOne
     */
    public function cateName()
    {
        return $this->hasOne(ActivityCategory::class, 'id', 'cid')->bind(['catename'=>'title']);
    }

    /**
     * 定制活动图片获取器
     * @param $value
     * @return array|false|string[]
     */
    protected function getImageInputAttr($value)
    {
        return explode(',', $value) ?: [];
    }

    /**
     * 定制活动分类搜索器
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCidAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('cid', $value);
        }
    }

    /**
     * 定制活动标题搜索器
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTitleAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * 热门定制活动搜索器
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsHotAttr($query, $value, $data)
    {
        $query->where('is_hot', $value);
    }

    /**
     * 轮播定制活动搜索器
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsBannerAttr($query, $value, $data)
    {
        $query->where('is_banner', $value);
    }

}
