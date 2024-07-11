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

namespace app\dao\system;

use app\dao\BaseDao;
use app\model\system\Relevance;
use app\services\system\RelevanceServices;
use think\db\BaseQuery;
use think\db\exception\DbException;

/**
 * Class RelevanceDao
 * @packge app\dao\system
 * @date : 2024/1/12 16:38
 * @author: Trump
 */
class RelevanceDao extends BaseDao
{
    /**
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/9 11:46
     * @author: Trump
     */
    protected function setModel(): string
    {
        return Relevance::class;
    }

    /**
     * @param int $id
     * @param $type
     * @param string $field
     * @return int
     * @throws DbException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:38
     * @author: Trump
     */
    public function clear(int $id, $type, string $field = 'left_id'): int
    {
        if (is_string($type)) $type = [$type];
        return $this->getModel()::getDb()->where($field, $id)->whereIn('type', $type)->delete();
    }

    /**
     * @param $where
     * @return BaseQuery
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:38
     * @author: Trump
     */
    public function joinUser($where): BaseQuery
    {
        $query = Relevance::hasWhere('community',function($query) use($where){
            $query->where('status',1)->where('is_show',1)->where('is_del',0);
            $query->when(isset($where['is_type']) && $where['is_type'] !== '',function($query) use($where){
                $query->where('is_type',$where['is_type']);
            });
        });
        $query->where('left_id',$where['uid'])->where('type',RelevanceServices::TYPE_COMMUNITY_START);
        return $query;
    }
}