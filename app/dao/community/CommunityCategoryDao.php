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


namespace app\dao\community;

use app\dao\BaseDao;
use app\model\community\CommunityCategory;

/**
 * Class CommunityCategoryDao
 * @packge app\dao\community
 * @date : 2024/1/8 10:16
 * @author: QSW
 * @email: qsw2012@outlook.com
 */
class CommunityCategoryDao extends BaseDao
{
    protected function setModel(): string
    {
        return CommunityCategory::class;
    }
}
