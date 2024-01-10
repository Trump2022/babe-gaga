<?php

namespace app\services\user\member;

use app\dao\user\UserBrokerageDao;
use app\dao\user\member\MemberInterestsDao;
use app\services\BaseServices;

/**
 * Class MemberInterestsServices
 * @packge app\services\user\member
 * @date : 2024/1/9 15:11
 * @author: QSW
 * @email: qsw2012@outlook.com
 * @mixin UserBrokerageDao
 */
class MemberInterestsServices extends BaseServices
{
    const TYPE_FREE = 1;

    //付费会员
    const TYPE_SVIP = 2;

    const HAS_TYPE_PRICE = 1;

    const HAS_TYPE_SIGN = 2;

    const HAS_TYPE_PAY = 3;

    const HAS_TYPE_SERVICE = 4;

    const HAS_TYPE_MEMBER = 5;

    const HAS_TYPE_COUPON = 6;

    //签到收益
    const INTERESTS_TYPE = [
        1 => ['label'=> '会员特价', 'msg' => ''],
        2 => ['label'=> '签到返利' , 'msg' => 'gaga币倍数' ],
        3 => ['label'=> '消费返利' , 'msg' => 'gaga币倍数' ],
        4 => ['label'=> '专属客服' , 'msg' => '' ],
        5 => ['label'=> '经验翻倍' , 'msg' => '经验翻倍' ],
        6 => ['label'=> '会员优惠券', 'msg' => ''],
    ];

    /**
     * @param MemberInterestsDao $dao
     */
    public function __construct(MemberInterestsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $has_type
     * @return mixed
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 15:13
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getSvipInterestVal($has_type)
    {
        return max(((float)$this->dao->query(['status' => 1])->where('has_type', $has_type)->where('type', 2)->value('value')) ?: 0, 0);
    }
}