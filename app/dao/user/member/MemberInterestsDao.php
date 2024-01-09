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

namespace app\dao\user\member;

use app\dao\BaseDao;
use app\model\user\member\MemberInterests;

/**
 * Class MemberInterestsDao
 * @packge app\dao\user\member
 * @date : 2024/1/9 15:06
 * @author: QSW
 * @email: qsw2012@outlook.com
 */
class MemberInterestsDao extends BaseDao
{
    /**
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 15:06
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    protected function setModel(): string
    {
        return MemberInterests::class;
    }
}