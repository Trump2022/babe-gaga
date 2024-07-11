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
declare (strict_types=1);

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserBill;
use crmeb\basic\BaseModel;
use think\Model;

/**
 * gaga币&经验
 * Class UserBillDao
 * @package app\dao\user
 */
class UserBillDao extends BaseDao
{

    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return UserBill::class;
    }

    /**
     * 获取列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @param array $typeWhere
     * @return array
     */
    public function getList(array $where, string $field = '*', int $page = 0, int $limit = 0, array $typeWhere = [])
    {
        return $this->search($where)->when(count($typeWhere) > 0, function ($query) use ($typeWhere) {
            $query->where($typeWhere);
        })->field($field)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('id desc')->select()->toArray();
    }

    /**
     * 获取列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getBillList(array $where, string $field = '*', int $page = 0, int $limit = 0)
    {
        return $this->search($where)->field($field)->with([
            'user' => function ($query) {
                $query->field('uid,nickname');
            }])->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('id desc')->select()->toArray();
    }

    /**
     * 获取某个条件总数
     * @param array $where
     */
    public function getBillSum(array $where)
    {
        return $this->search($where)->sum('number');
    }


    /**
     * 获取某个条件总条数
     * @param array $where
     */
    public function getBillCount(array $where)
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * 获取某些条件的bill总数
     * @param array $where
     * @return mixed
     */
    public function getBillSumColumn(array $where)
    {
        if (isset($where['uid']) && is_array($where['uid'])) {
            return $this->search($where)->group('uid')->column('sum(number) as num', 'uid');
        } else
            return $this->search($where)->sum('number');
    }

    /**
     * 获取类型
     * @param array $where
     * @param string $filed
     * @return mixed
     */
    public function getType(array $where, string $filed = 'title,type')
    {
        return $this->search($where)->distinct(true)->field($filed)->group('type')->select();
    }

    /**
     * 获取签到用户数量
     * @param array $where
     * @return mixed
     */
    public function getUserSignPoint(array $where)
    {
        return $this->search($where)->count();
    }

    /**
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserBillList(array $where)
    {
        return $this->search($where)->select()->toArray();
    }

    /**
     * 获取佣金排行
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function brokerageRankList(array $where, int $page = 0, int $limit = 0)
    {
        return $this->search($where)->field('uid,SUM(IF(pm=1,`number`,-`number`)) as brokerage_price')->with(['user' => function ($query) {
            $query->field('uid,avatar,nickname');
        }])->order('brokerage_price desc')->group('uid')->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }

    /**
     * 时间分组
     * @param array $where
     * @param string $filed
     * @param string $group
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getUserBillListByGroup(array $where, string $filed, string $group, int $page, int $limit)
    {
        return $this->search($where)->field($filed)->where('number', '>', 0)->order('add_time desc')->group($group)->page($page, $limit)->select()->toArray();
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBalanceRecord(array $where, int $page, int $limit)
    {
        return $this->search($where)->order('add_time desc')->page($page, $limit)->select()->toArray();
    }

    /**
     * 计算某个条件下订单内商品总数
     * @param $where
     * @return float|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTotalSum(array $where)
    {
        $list = $this->search($where)->with('order')->select()->toArray();
        if (count($list)) {
            $sum = 0;
            foreach ($list as $item) {
                $sum += $item['total_num'];
            }
            return $sum;
        } else {
            return 0;
        }
    }

    /**
     * 获取某个字段总和
     * @param array $where
     * @param string $field
     * @return float
     */
    public function getWhereSumField(array $where, string $field)
    {
        return $this->search($where)
            ->when(isset($where['timeKey']), function ($query) use ($where) {
                $query->whereBetweenTime('add_time', $where['timeKey']['start_time'], $where['timeKey']['end_time']);
            })
            ->sum($field);
    }

    /**
     * 根据某字段分组查询
     * @param array $where
     * @param string $field
     * @param string $group
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupField(array $where, string $field, string $group)
    {
        return $this->search($where)
            ->when(isset($where['timeKey']), function ($query) use ($where, $field, $group) {
                $query->whereBetweenTime('add_time', $where['timeKey']['start_time'], $where['timeKey']['end_time']);
                if ($where['timeKey']['days'] == 1) {
                    $timeUinx = "%H";
                } elseif ($where['timeKey']['days'] == 30) {
                    $timeUinx = "%Y-%m-%d";
                } elseif ($where['timeKey']['days'] == 365) {
                    $timeUinx = "%Y-%m";
                } elseif ($where['timeKey']['days'] > 1 && $where['timeKey']['days'] < 30) {
                    $timeUinx = "%Y-%m-%d";
                } elseif ($where['timeKey']['days'] > 30 && $where['timeKey']['days'] < 365) {
                    $timeUinx = "%Y-%m";
                } else {
					$timeUinx = "%Y-%m";
                }
                $query->field("sum($field) as number,FROM_UNIXTIME($group, '$timeUinx') as time");
                $query->group("FROM_UNIXTIME($group, '$timeUinx')");
            })
            ->order('add_time ASC')->select()->toArray();
    }

	/**
	 * gaga币趋势
	 * @param $time
	 * @param $timeType
	 * @param $field
	 * @param $str
	 * @return mixed
	 */
	public function getPointTrend($time, $timeType, $field, $str, $orderStatus = '')
	{
		return $this->getModel()->where(function ($query) use ($field, $orderStatus) {
			$query->where('category', 'integral');
			if ($orderStatus == 'add') {
				$query->where('pm', 1);
			} elseif ($orderStatus == 'sub') {
				$query->where('pm', 0);
			}
		})->where(function ($query) use ($time, $field) {
			if ($time[0] == $time[1]) {
				$query->whereDay($field, $time[0]);
			} else {
				$query->whereTime($field, 'between', $time);
			}
		})->field("FROM_UNIXTIME($field,'$timeType') as days,$str as num")->group('days')->select()->toArray();
	}

    public function search(array $where = [])
    {
        return parent::search($where); // TODO: Change the autogenerated stub
    }


    public function searchJoin(array $where)
    {
        return UserBill::getDB()->alias('a')->leftJoin('User b', 'a.uid = b.uid')
            ->field('a.bill_id,a.pm,a.title,a.number,a.balance,a.mark,a.create_time,a.status,b.nickname,a.uid,a.category')
            ->when(isset($where['mer_id']) && $where['mer_id'] !== '', function ($query) use ($where) {
                $query->where('a.mer_id', $where['mer_id']);
            })
            ->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where) {
                $data = explode('/', $where['type'], 2);
                if (count($data) > 1) {
                    $query->where('a.category', $data[0])->where('type', $data[1]);
                } else {
                    $query->where('a.type', $where['type']);
                }
            })
            ->when(isset($where['date']) && $where['date'] !== '', function ($query) use ($where) {
                getModelTime($query, $where['date'], 'a.create_time');
            })
            ->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
                $query->whereLike('a.uid|b.nickname|a.title', "%{$where['keyword']}%");
            })
            ->when(isset($where['category']) && $where['category'] !== '', function ($query) use ($where) {
                $query->where('a.category', $where['category']);
            })->where('category', '<>', 'sys_brokerage');

    }

    public function lockIntegral($uid = null, $order_id = null)
    {
        $lst = UserBill::getDB()->where('category', 'integral')
            ->where('type', 'lock')->when($order_id, function ($query, $order_id) {
                $query->where('link_id', $order_id);
            })->when($uid, function ($query, $uid) {
                $query->where('uid', $uid);
            })->where('status', 0)->field('link_id,number')->select()->toArray();
        $lockIntegral = 0;
        if (count($lst)) {
            $lockIntegral = -1 * UserBill::getDB()->whereIn('link_id', array_column($lst, 'link_id'))->where('uid', $uid)
                    ->where('category', 'integral')->where('type', 'refund_lock')->sum('number');
        }
        foreach ($lst as $bill) {
            $lockIntegral = bcadd($lockIntegral, $bill['number'], 0);
        }
        return $lockIntegral;
    }

    /**
     * 最后一条积分获得记录
     * @param $where
     * @return array|BaseModel|mixed|Model
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/17 19:45
     * @author: Trump
     */
    public function getLastGiveIntegral($where)
    {
        return $this->getModel()->where($where)->order('id desc')->limit(1)->findOrEmpty();
    }
}
