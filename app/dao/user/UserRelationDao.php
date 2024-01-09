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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserRelation;

/**
 * Class UserRelationDao
 * @package app\dao\user
 */
class UserRelationDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return UserRelation::class;
    }

    /**
     * 获取收藏列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', array $with = [], int $page = 0, int $limit = 0)
    {
        $is_class = $where['is_class'] ?? 0;
        if(!empty($where['is_class'])){
            unset($where['is_class']);
        }
        return $this->search($where)->field($field)->when($with, function($query) use ($with) {
			$query->with($with);
        })->whereExists(function ($query) use ($is_class) {
            $query->table('gaga_store_product')->where('is_class', $is_class)
                ->whereRaw('gaga_store_product.id = gaga_user_relation.relation_id');
        })->when($page && $limit, function($query) use ($page, $limit) {
			$query->page($page, $limit);
		})->order('add_time desc')->select()->toArray();

    }

    /**
     * 获取收藏列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function relationCount(array $where)
    {
        $is_class = $where['is_class'] ?? 0;
        if (!empty($where['is_class'])) {
            unset($where['is_class']);
        }
        return $this->search($where)->whereExists(function ($query) use ($is_class) {
            $query->table('gaga_store_product')->where('is_class', $is_class)
                ->whereRaw('gaga_store_product.id = gaga_user_relation.relation_id');
        })->count();
    }
}
