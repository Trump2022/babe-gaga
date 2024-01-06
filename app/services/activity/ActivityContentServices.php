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

namespace app\services\activity;

use app\dao\activity\ActivityContentDao;
use app\services\BaseServices;

/**
 * 定制活动内容
 * Class ActivityContentServices
 * @package app\services\activity
 * @mixin ActivityContentDao
 */
class ActivityContentServices extends BaseServices
{
    /**
     * ActivityContentServices constructor.
     * @param ActivityContentDao $dao
     */
    public function __construct(ActivityContentDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 删除
     * @param int $id
     * @return bool
     */
    public function del(int $id)
    {
        return $this->dao->del($id);
    }
}
