<?php

namespace app\model\user;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class UserBrokerages extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'user_brokerage_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'user_brokerages';


    public static function tablePk(): ?string
    {
        return 'user_brokerage_id';
    }

    public static function tableName(): string
    {
        return 'user_brokerages';
    }

    public function getBrokerageRuleAttr($val)
    {
        return json_decode($val, true);
    }

    public function setBrokerageRuleAttr($val)
    {
        return json_encode($val, JSON_UNESCAPED_UNICODE);
    }

    public function getExtensionOneRateAttr()
    {
        return bcdiv((int)$this->extension_one, 100, 4);
    }

    public function getExtensionTwoRateAttr()
    {
        return bcdiv((int)$this->extension_two, 100, 4);
    }
}