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


namespace app\model\community;

use app\services\system\RelevanceServices;
use crmeb\basic\BaseModel;
use app\model\store\product\Spu;
use app\model\system\Relevance;
use app\model\user\User;
use crmeb\traits\ModelTrait;
use think\model\relation\HasMany;


class Community extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'community_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'community';

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 10/26/21
     */
    public static function tablePk(): string
    {
        return 'community_id';
    }

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 10/26/21
     */
    public static function tableName(): string
    {
        return 'community';
    }

    public function getImageAttr($value)
    {
        return explode(',',$value);
    }

    public function author()
    {
        return $this->hasOne(User::class,'uid','uid');
    }

    public function topic()
    {
        return $this->hasOne(CommunityTopic::class,'topic_id','topic_id');
    }

    public function reply()
    {
        return $this->hasMany(CommunityReply::class,'community_id','community_id');
    }

    /**
     * @return HasMany
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/19 11:31
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function relevance(): HasMany
    {
        return $this->hasMany(Relevance::class, 'left_id','community_id')->where('type',RelevanceServices::TYPE_COMMUNITY_PRODUCT);
    }

    /*
     *  右侧为内容ID的
     */
    public function relevanceRight()
    {
        return $this->hasMany(Relevance::class, 'right_id','community_id');
    }

    public function isStart()
    {
        return $this->hasOne(Relevance::class, 'right_id','community_id')->where('type', RelevanceServices::TYPE_COMMUNITY_START)->bind(['relevance_id']);
    }

    public function isFans()
    {
        return $this->hasOne(Relevance::class, 'right_id','uid')->where('type', RelevanceServices::TYPE_COMMUNITY_FANS)->bind(['is_fans' => 'right_id']);
    }

    public function category()
    {
        return $this->hasOne(CommunityCategory::class, 'category_id','category_id');
    }

    public function getTimeAttr()
    {
        return date('m月d日',strtotime($this->create_time));
    }

    public function getCountReplyAttr()
    {
        return CommunityReply::where('community_id',$this->community_id)->where('status',1)->count();
    }

    public function searchTopicIdAttr($query, $value)
    {
        $query->where('topic_id', $value);
    }

    public function searchTitleAttr($query, $value)
    {
        $query->whereLike('title', "%{$value}%");
    }

    public function searchKeywordAttr($query, $value)
    {
        $query->whereLike('title|content', "%{$value}%");
    }

    public function searchCategoryIdAttr($query, $value)
    {
        $query->where('category_id', $value);
    }

    public function searchIsShowAttr($query, $value)
    {
        $query->where('is_show', $value);
    }

    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    public function searchUidAttr($query, $value)
    {
        $query->where('uid', $value);
    }

    public function searchIsHotAttr($query, $value)
    {
        $query->where('is_hot', $value);
    }

    public function searchUidsAttr($query, $value)
    {
        $query->whereIn('uid', $value);
    }

    public function searchSpuIdAttr($query, $value)
    {
        $id = Relevance::where('right_id',$value)
            ->where('type',RelevanceServices::TYPE_COMMUNITY_PRODUCT)
            ->column('left_id');
        $query->where('community_id','in', $id);
    }

    public function searchIsTypeAttr($query, $value)
    {
        $query->whereIn('is_type', $value);
    }

    public function searchCommunityIdAttr($query, $value)
    {
        $query->where('community_id', $value);
    }

    public function searchNotIdAttr($query, $value)
    {
        $query->where('community_id', '<>',$value);
    }

}
