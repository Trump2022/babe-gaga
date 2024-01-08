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

namespace app\dao\activity;


use app\dao\BaseDao;
use app\model\activity\ActivityCategory;

/**
 * 定制活动分类
 * Class ActivityCategoryDao
 * @package app\dao\activity
 */
class ActivityCategoryDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return ActivityCategory::class;
    }

    /**
     * 获取定制活动列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getList(array $where, int $page, int $limit)
    {
        return $this->search($where)->page($page, $limit)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * 前台获取定制活动分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getActivityCategory($where = [])
    {
        $id = $where['id'] ?? 0;
        return $this->search(['hidden'=>0,'is_del'=>0,'status'=>1])
            ->when((int)$id > 0, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->order('sort DESC')
            ->field('id,title')
            ->select()->toArray();
    }
}
