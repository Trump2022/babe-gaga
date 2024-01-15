<?php

namespace app\model\user;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class MemberInterests extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'interests_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'member_interests';

    public static function tablePk(): ?string
    {
        return 'interests_id';
    }

    public static function tableName(): string
    {
        return 'member_interests';
    }

    public function searchTypeAttr($query, $value)
    {
        $query->where('type',$value);
    }

    public function searchInterestsIdAttr($query, $value)
    {
        $query->where('interests_id',$value);
    }

    public function searchNameAttr($query, $value)
    {
        $query->whereLike('name',"%{$value}%");
    }

    public function searchBrokerageLevelAttr($query, $value)
    {
        $query->where('brokerage_level',$value);
    }

    public function searchLevelAttr($query, $value)
    {
        $query->where('brokerage_level', '<=', $value);
    }

    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }
}