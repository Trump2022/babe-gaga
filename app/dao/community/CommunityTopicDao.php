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
use app\model\community\CommunityTopic;

/**
 * Class CommunityTopicDao
 * @packge app\dao\community
 * @date : 2024/1/9 19:13
 * @author: QSW
 * @email: qsw2012@outlook.com
 */
class CommunityTopicDao extends BaseDao
{
    /**
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 10:16
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    protected function setModel(): string
    {
        return CommunityTopic::class;
    }

    public function countInc(int $id, string $filed, int $inc = 1)
    {
        return $this->getModel()::getDb()->where($this->getPk(), $id)->inc($filed, $inc)->update();
    }

    public function countDec(int $id, string $filed, int $dec = 1)
    {
        try{
            return $this->getModel()::getDb()->where($this->getPk(), $id)->dec($filed, $dec)->update();
        } catch (\Exception $exception) {

        }
    }
}
