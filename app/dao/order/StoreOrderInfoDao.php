<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\dao\order;


use app\dao\BaseDao;
use app\model\order\StoreOrderInfo;

/**
 * 订单详情
 * Class StoreOrderInfoDao
 * @package app\dao\order
 * @method saveAll(array $data)
 */
class StoreOrderInfoDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StoreOrderInfo::class;
    }

    /**
     * 获取下单信息详情
     * @param array $where
     * @param array $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderInfo(array $where, array $field = ['*'])
    {
        return $this->getModel()->where($where)->field($field)->find();
    }

    /**
     * 保存下单信息
     * @param int $oid
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveOrderInfo(int $oid, array $data)
    {
        $row = $this->getModel()->where(['oid' => $oid])->find();
        if ($row) {
            unset($data['oid']);
            $data['add_time'] = $data['update_time'] = time();
            $res = $this->getModel()->where('id', $row['id'])->update($data);
        } else {
            $data['oid'] = $oid;
            $data['add_time'] = time();
            $res = $this->getModel()->insertGetId($data);
        }
        return $res;
    }
}
