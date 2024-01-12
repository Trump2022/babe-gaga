<?php

namespace app\model\user;

use app\model\store\product\Product;
use app\model\store\product\Spu;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class UserRelations extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'uid';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'user_relations';

    public static function tablePk(): ?string
    {
        return 'uid';
    }

    public static function tableName(): string
    {
        return 'user_relations';
    }

    public function product()
    {
        return $this->hasOne(Product::class,'product_id','type_id');
    }

    public function spu()
    {
        return $this->hasOne(Spu::class,'spu_id','type_id');
    }
}