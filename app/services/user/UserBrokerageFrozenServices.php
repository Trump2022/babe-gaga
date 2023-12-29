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

namespace app\services\user;


use app\dao\user\UserBrokerageFrozenDao;
use app\services\BaseServices;

/**
 * 佣金冻结
 * Class UserBrokerageFrozenServices
 * @package app\services\user
 * @mixin UserBrokerageFrozenDao
 */
class UserBrokerageFrozenServices extends BaseServices
{
    /**
     * UserBrokerageFrozenServices constructor.
     * @param UserBrokerageFrozenDao $dao
     */
    public function __construct(UserBrokerageFrozenDao $dao)
    {
        $this->dao = $dao;
    }

}
