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


namespace app\model\user;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;
use app\model\store\product\Spu;

class UserHistory extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'user_history_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'user_history';

    public static function tablePk(): ?string
    {
        return 'user_history_id';
    }

    public static function tableName(): string
    {
        return 'user_history';
    }

    public function getUpdateTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public function getStopTimeAttr()
    {
        if (!$this->spu) return '';
        if ($this->spu->product_type == 1 && $this->spu->seckillActive) {
            $day = date('Y-m-d', time());
            $_day = strtotime($day);
            $end_day = strtotime($this->spu->seckillActive['end_day']);
            if ($end_day >= $_day)
                return strtotime($day . $this->spu->seckillActive['end_time'] . ':00:00');
            if ($end_day < strtotime($day))
                return strtotime(date('Y-m-d', $end_day) . $this->spu->seckillActive['end_time'] . ':00:00');
        }
        return '';
    }

    public function spu()
    {
        return $this->hasOne(Spu::class,'spu_id','res_id');
    }

    public function searchUidAttr($query,$value)
    {
        $query->where('uid',$value);
    }

    public function searchHistoryIdAttr($query,$value)
    {
        $query->where('history_id',$value);
    }
    public function searchHistoryIdsAttr($query,$value)
    {
        $query->where('history_id','in',$value);
    }
}
