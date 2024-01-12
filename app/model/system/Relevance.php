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

namespace app\model\system;

use app\model\user\User;
use app\model\community\Community;
use app\model\store\product\Spu;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\model\relation\HasOne;

class Relevance extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'relevance_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'relevance';

    public static function tablePk(): string
    {
        return 'relevance_id';
    }

    public static function tableName(): string
    {
        return 'relevance';
    }

    /**
     * @return HasOne
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:28
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function spu(): HasOne
    {
        return $this->hasOne(Spu::class, 'spu_id', 'right_id');
    }

    /**
     * @return HasOne
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:43
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function community(): HasOne
    {
        return $this->hasOne(Community::class,'community_id','right_id')
            ->bind(['community_id','title','image','start','uid','create_time','count_start','author','is_type']);
    }

    /**
     * @return HasOne
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:10
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function fans(): HasOne
    {
        return $this->hasOne(User::class,'uid','left_id');
    }

    /**
     * @return HasOne
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 17:10
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function focus(): HasOne
    {
        return $this->hasOne(User::class,'uid','right_id');
    }
}