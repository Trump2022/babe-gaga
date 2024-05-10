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

namespace app\services\user;

use app\dao\BaseDao;
use app\jobs\notice\SmsJob;
use app\services\user\UserServices;
use app\services\user\UserSignServices;
use app\services\BaseServices;
use app\dao\user\UserBillDao;
use app\services\pc\ProductServices;
use app\services\user\level\UserLevelServices;
use crmeb\basic\BaseModel;
use crmeb\jobs\SendSmsJob;
use think\Exception;
use think\exception\ValidateException;
use crmeb\services\CacheService;
use think\facade\Log;
use think\facade\Queue;
use think\Model;

/**
 *
 * Class UserBillServices
 * @package app\services\user
 * @mixin UserBillDao
 */
class UserBillServices extends BaseServices
{
    /**
     * @var UserBillDao
     */
    protected $dao;

    /**
     * 用户记录模板
     * @var array[]
     */
    protected $incomeData = [
        'one_spread_give_brokerage' => [
            'title' => '一级返佣获得gaga币',
            'category' => 'integral',
            'type' => 'one_spread_add',
            'mark' => '一级返佣获得{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'one_spread_brokerage_refund' => [
            'title' => '扣除一级返佣gaga币',
            'category' => 'integral',
            'type' => 'one_spread_add',
            'mark' => '扣除一级返佣{%num%}gaga币',
            'status' => 1,
            'pm' => 0
        ],
        'create_community_give_integral' => [
            'title' => '种草图文获得积分(gaga币)',
            'category' => 'integral',
            'type' => 'create_community_add',
            'mark' => '种草图文赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'create_community_integral_refund' => [
            'title' => '种草图文撤销审核扣除积分(gaga币)',
            'category' => 'integral',
            'type' => 'create_community_sub',
            'mark' => '种草图文撤销审核扣除{%num%}gaga币',
            'status' => 1,
            'pm' => 0
        ],
        'pay_give_integral' => [
            'title' => '购买商品赠送gaga币',
            'category' => 'integral',
            'type' => 'gain',
            'mark' => '购买商品赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'order_give_integral' => [
            'title' => '下单赠送gaga币',
            'category' => 'integral',
            'type' => 'gain',
            'mark' => '下单赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'order_promotions_give_integral' => [
            'title' => '下单优惠活动赠送gaga币',
            'category' => 'integral',
            'type' => 'gain',
            'mark' => '下单优惠活动赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'order_give_exp' => [
            'title' => '下单赠送经验',
            'category' => 'exp',
            'type' => 'gain',
            'mark' => '下单赠送{%num%}经验',
            'status' => 1,
            'pm' => 1
        ],
        'integral_refund' => [
            'title' => 'gaga币回退',
            'category' => 'integral',
            'type' => 'deduction',
            'mark' => '购买商品失败,回退{%num%}gaga币',
            'status' => 1,
            'pm' => 0
        ],
        'order_integral_refund' => [
            'title' => '返还下单使用gaga币',
            'category' => 'integral',
            'type' => 'integral_refund',
            'mark' => '购买商品失败,回退{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'pay_product_integral_back' => [
            'title' => '商品退gaga币',
            'category' => 'integral',
            'type' => 'pay_product_integral_back',
            'mark' => '订单返还{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'deduction' => [
            'title' => 'gaga币抵扣',
            'category' => 'integral',
            'type' => 'deduction',
            'mark' => '购买商品使用{%number%}gaga币抵扣{%deductionPrice%}元',
            'status' => 1,
            'pm' => 0
        ],
        'lottery_use_integral' => [
            'title' => '参与抽奖使用gaga币',
            'category' => 'integral',
            'type' => 'lottery_use',
            'mark' => '参与抽奖使用{%num%}gaga币',
            'status' => 1,
            'pm' => 0
        ],
        'lottery_give_integral' => [
            'title' => '抽奖中奖赠送gaga币',
            'category' => 'integral',
            'type' => 'lottery_add',
            'mark' => '抽奖中奖赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'storeIntegral_use_integral' => [
            'title' => 'gaga币兑换商品',
            'category' => 'integral',
            'type' => 'storeIntegral_use',
            'mark' => 'gaga币商城兑换商品使用{%num%}gaga币',
            'status' => 1,
            'pm' => 0
        ],
        'system_clear_integral' => [
            'title' => '到期自动清除gaga币',
            'category' => 'integral',
            'type' => 'system_clear',
            'mark' => '到期自动清除{%num%}gaga币',
            'status' => 1,
            'pm' => 0
        ],
        'newcomer_give_integral' => [
            'title' => '新人礼赠送gaga币',
            'category' => 'integral',
            'type' => 'newcomer_add',
            'mark' => '新人礼赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
        'level_give_integral' => [
            'title' => '会员卡激活赠送gaga币',
            'category' => 'integral',
            'type' => 'level_add',
            'mark' => '会员卡激活赠送{%num%}gaga币',
            'status' => 1,
            'pm' => 1
        ],
		'system_add_integral' => [
			'title' => '系统增加gaga币',
			'category' => 'integral',
			'type' => 'system_add',
			'mark' => '系统增加了{%num%}gaga币',
			'status' => 1,
			'pm' => 1
		],
		'system_sub_integral' => [
			'title' => '系统减少gaga币',
			'category' => 'integral',
			'type' => 'system_sub',
			'mark' => '系统扣除了{%num%}gaga币',
			'status' => 1,
			'pm' => 0
		],
    ];

    /**
     * UserBillServices constructor.
     * @param UserBillDao $dao
     */
    public function __construct(UserBillDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param string $key
     * @return array|array[]
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/17 19:23
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getIncomeData(string $key = ''): array
    {
        return strlen($key) ? $this->incomeData[$key] : $this->incomeData;
    }


    public function userList($where, $uid, $page, $limit)
    {
        $where['uid'] = $uid;
        $query = $this->dao->search($where)->order('create_time DESC');
        $count = $query->count();
        $list = $query->setOption('field', [])->field('bill_id,pm,title,number,balance,mark,create_time,status')->page($page, $limit)->select();
        return compact('count', 'list');
    }

    public function month(array $where)
    {
        $group = $this->dao->search($where)->field('FROM_UNIXTIME(unix_timestamp(create_time),"%Y-%m") as time')
            ->order('time DESC')->group('time')->select();
        $ret = [];
        foreach ($group as $k => $item){
            $ret[$k]['month'] = $item['time'];
            $query = $this->dao->getSearch($where)->field('title,number,create_time')->whereMonth('create_time',$item['time']);
            $ret[$k]['list'] = $query->order('create_time DESC')->select();
        }
        return $ret;
    }


    public function getList($where, $page, $limit)
    {
        $query = $this->dao->searchJoin($where)->order('a.create_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    public function getLst($where, $page, $limit)
    {
        $query = $this->dao->search($where)->order('create_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }


    /**
     * @param int $uid
     * @param string $category
     * @param string $type
     * @param int $pm
     * @param array $data
     * @return BaseModel|Model
     * @author xaboy
     * @day 2020-05-07
     */
    public function bill(int $uid, string $category, string $type, int $pm, array $data)
    {
        $data['category'] = $category;
        $data['type'] = $type;
        $data['uid'] = $uid;
        $data['pm'] = $pm;
        $bill = $this->dao->create($data);
        if($category == 'now_money'){
            SmsJob::dispatch([request()->user()->phone, $data, 'USER_BALANCE_CHANGE']);
        }
        return $bill;
    }

    /**
     * @param int $uid
     * @param string $category
     * @param string $type
     * @param array $data
     * @return BaseModel|Model
     * @author xaboy
     * @day 2020-05-07
     */
    public function incBill(int $uid, string $category, string $type, array $data)
    {
        return $this->bill($uid, $category, $type, 1, $data);
    }

    /**
     * @param int $uid
     * @param string $category
     * @param string $type
     * @param array $data
     * @author xaboy
     * @day 2020-05-07
     */
    public function decBill(int $uid, string $category, string $type, array $data)
    {
        return $this->bill($uid, $category, $type, 0, $data);
    }

    /**
     * TODO 积分日志头部统计
     * @return array
     * @author Qinii
     * @day 6/9/21
     */
    public function getStat($merId = 0)
    {
        if($merId){
            $isusd = app()->make(ProductServices::class)->getSearch(['mer_id' => $merId])->sum('integral_total');
            $refund = $this->dao->search(['category' => 'mer_integral','type' => 'refund','mer_id' => $merId])->sum('number');
            $numb = app()->make(ProductServices::class)->getSearch(['mer_id' => $merId])->sum('integral_price_total');
            return [
                [
                    'className' => 'el-icon-s-cooperation',
                    'count' => $isusd,
                    'field' => '个',
                    'name' => '已使用积分（分）'
                ],
                [
                    'className' => 'el-icon-edit',
                    'count' => $refund,
                    'field' => '次',
                    'name' => '退款订单返回积分（分）'
                ],
                [
                    'className' => 'el-icon-edit',
                    'count' => $numb,
                    'field' => '次',
                    'name' => '积分抵扣金额（元）'
                ],
            ];
        }
        // 总积分
        $integral = app()->make(UserServices::class)->search(['status' => 1])->sum('integral');
        // 客户签到次数
        $sign = app()->make(UserSignServices::class)->getSearch([])->count('*');
        // 签到送出积分
        $sign_integral = $this->dao->search(['type' => 'sign_integral'])->sum('number');
        // 使用积分
        $isusd  = $this->dao->search(['category' => 'integral','type' => 'deduction'])->sum('number');
        $refund = $this->dao->search(['category' => 'mer_integral','type' => 'refund'])->sum('number');
        $order = $isusd - $refund;

        // 下单赠送积分
        $order_integral1 = $this->dao->search(['category' => 'integral','type' => 'lock'])->sum('number');
        $order_integral2 = $this->dao->search(['category' => 'integral','type' => 'refund_lock'])->sum('number');
        $order_integral = $order_integral1 - $order_integral2;
        $order_integral = $order_integral < 0 ? 0 : $order_integral;
        // 冻结积分
        $freeze_integral = $this->dao->lockIntegral();

        return [
            [
                'className' => 'el-icon-s-cooperation',
                'count' => $integral,
                'field' => '个',
                'name' => '总积分'
            ],
            [
                'className' => 'el-icon-edit',
                'count' => $sign,
                'field' => '次',
                'name' => '客户签到次数'
            ],
            [
                'className' => 'el-icon-s-goods',
                'count' => $sign_integral ,
                'field' => '个',
                'name' => '签到送出积分'
            ],
            [
                'className' => 'el-icon-s-order',
                'count' => $order,
                'field' => '个',
                'name' => '使用积分'
            ],
            [
                'className' => 'el-icon-present',
                'count' => $order_integral,
                'field' => '个',
                'name' => '下单赠送积分'
            ],
            [
                'className' => 'el-icon-warning',
                'count' => $freeze_integral,
                'field' => '',
                'name' => '冻结积分'
            ],
        ];
    }

    /**
     *  获取用户记录总和
     * @param int $uid
     * @param string $category
     * @param array $type
     * @param string $time
     * @param bool $pm
     * @return mixed
     */
    public function getRecordCount(int $uid, $category = 'now_money', $type = [], $time = '', $pm = false)
    {

        $where = [];
        $where['uid'] = $uid;
        $where['category'] = $category;
        $where['status'] = 1;

        if (is_string($type) && strlen(trim($type))) {
            $where['type'] = explode(',', $type);
        }
        if ($time) {
            $where['time'] = $time;
        }

		$where['pm'] = $pm ? 1 : 0;

        return $this->dao->getBillSumColumn($where);
    }

    /**
     * 获取gaga币列表
     * @param int $uid
     * @param array $where_time
     * @param string $field
     * @return array
     */
    public function getIntegralList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => 'integral'];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        foreach ($list as &$item) {
            $item['number'] = intval($item['number']);
			$item['balance'] = intval($item['balance']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 获取签到列表
     * @param int $uid
     * @param array $where_time
     * @param string $field
     * @return array
     */
    public function getSignList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => 'integral', 'type' => 'sign'];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 经验总数
     * @param int $uid
     * @param array $where_time
     * @return float
     */
    public function getExpSum(int $uid = 0, $where_time = [])
    {
        $where = ['category' => ['exp'], 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['time'] = $where_time;
        return $this->dao->getBillSum($where);
    }

    /**
     * 获取所有经验列表
     * @param int $uid
     * @param array $where_time
     * @param string $field
     * @return array
     */
    public function getExpList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => ['exp']];
        $where['status'] = 1;
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

	/**
	 * 记录gaga币变化
	 * @param int $uid
	 * @param string $type
	 * @param array $data
	 * @param int $pm
	 * @return bool
	 * @throws Exception
	 */
    public function incomeIntegral(int $uid, string $type, array $data, int $pm = 1)
    {
        $data['uid'] = $uid;
        $data['category'] = 'integral';
        $data['type'] = $type;
        $data['pm'] = $pm;
        $data['status'] = 1;
        $data['add_time'] = time();
        if (!$this->dao->save($data))
            throw new Exception('增加记录失败');
        return true;
    }

    /**
     * @param string $type | 写入类型
     * @param int $uid
     * @param $number
     * @param $balance
     * @param mixed $link_id
     * @return bool|BaseModel|Model
     */
    public function income(string $type, int $uid, $number, $balance, $link_id = 0)
    {
        $data = $this->incomeData[$type] ?? null;
        if (!$data) {
            return true;
        }
        $data['uid'] = $uid;
        $data['balance'] = $balance ?? 0;
        $data['link_id'] = $link_id;
        if (is_array($number)) {
            $key = array_keys($number);
            $key = array_map(function ($item) {
                return '{%' . $item . '%}';
            }, $key);
            $value = array_values($number);
            $data['number'] = $number['number'] ?? 0;
            $data['mark'] = str_replace($key, $value, $data['mark']);
        } else {
            $data['number'] = $number;
            $data['mark'] = str_replace(['{%num%}'], $number, $data['mark']);
        }
        $data['add_time'] = time();
		if ((float)$data['number']) {
			return $this->dao->save($data);
		}
        return true;

    }

    /**
     * 邀请新用户增加经验
     * @param int $spreadUid
     */
    public function inviteUserIncExp(int $spreadUid)
    {
        if (!$spreadUid) {
            return false;
        }
        //用户等级是否开启
        if (!sys_config('member_func_status', 1)) {
            return false;
        }
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $spread_user = $userService->getUserInfo($spreadUid);
        if (!$spread_user) {
            return false;
        }
        $exp_num = sys_config('invite_user_exp', 0);
        if ($exp_num) {
            $userService->incField($spreadUid, 'exp', (int)$exp_num);
            $data = [];
            $data['uid'] = $spreadUid;
            $data['number'] = $exp_num;
            $data['category'] = 'exp';
            $data['type'] = 'invite_user';
            $data['title'] = $data['mark'] = '邀新奖励';
            $data['balance'] = (int)$spread_user['exp'];
            $data['pm'] = 1;
            $data['status'] = 1;
            $this->dao->save($data);
        }
        //检测会员等级
        try {
            /** @var UserLevelServices $levelServices */
            $levelServices = app()->make(UserLevelServices::class);
            //检测会员升级
            $levelServices->detection($spreadUid);
        } catch (\Throwable $e) {
            Log::error('会员等级升级失败,失败原因:' . $e->getMessage());
        }
        return true;
    }

    /**
     * 获取type
     * @param array $where
     * @param string $filed
     */
    public function getBillType(array $where)
    {
        return $this->dao->getType($where);
    }

    /**
     * 资金类型
     */
    public function bill_type()
    {
        $where = [];
        $where['not_type'] = ['gain', 'system_sub', 'deduction', 'sign'];
        $where['not_category'] = ['exp', 'integral'];
        return CacheService::get('user_type_list', function () use ($where) {
            return ['list' => $this->dao->getType($where)];
        }, 600);
    }

    /**
     * 记录分享次数
     * @param int $uid 用户uid
     * @param int $cd 冷却时间
     * @return Boolean
     * */
    public function setUserShare(int $uid, $cd = 300)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ValidateException('用户不存在！');
        }
        $cachename = 'Share_' . $uid;
        if (CacheService::get($cachename)) {
            return false;
        }
        $data = ['title' => '用户分享记录', 'uid' => $uid, 'category' => 'share', 'type' => 'share', 'number' => 0, 'link_id' => 0, 'balance' => 0, 'mark' => date('Y-m-d H:i:s', time()) . ':用户分享'];
        if (!$this->dao->save($data)) {
            throw new ValidateException('记录分享记录失败');
        }
        CacheService::set($cachename, 1, $cd);
        return true;
    }

    /**
     * 获取gaga币列表
     * @param array $where
     * @param string $field
     * @param int $limit
     * @return array
     */
    public function getPointList(array $where, string $field = '*', int $limit = 0)
    {
        $where_data = [];
        $where_data['category'] = 'integral';
        if (isset($where['uid']) && $where['uid'] != '') {
            $where_data['uid'] = $where['uid'];
        }
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $where_data['time'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        if (isset($where['type']) && $where['type'] != '') {
            $where_data['type'] = $where['type'];
        }
        if (isset($where['nickname']) && $where['nickname'] != '') {
            $where_data['like'] = $where['nickname'];
        }
        if (isset($where['excel']) && $where['excel'] != '') {
            $where_data['excel'] = $where['excel'];
        } else {
            $where_data['excel'] = 0;
        }
        if ($limit) {
            [$page] = $this->getPageValue();
        } else {
            [$page, $limit] = $this->getPageValue();
        }
        $list = $this->dao->getBillList($where_data, $field, $page, $limit);
        foreach ($list as &$item) {
            $item['nickname'] = $item['user']['nickname'] ?? '';
            $item['number'] = intval($item['number']);
            $item['balance'] = intval($item['balance']);
            unset($item['user']);
        }
        $count = $this->dao->count($where_data);
        return compact('list', 'count');
    }

    /**
     * gaga币头部信息
     * @param array $where
     * @return array[]
     */
    public function getUserPointBadgelist(array $where): array
    {
        $data = [];
        $where_data = [];
        $where_data['category'] = 'integral';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $where_data['time'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        if (isset($where['nickname']) && $where['nickname'] != '') {
            $where_data['like'] = $where['nickname'];
        }
		$data['SumIntegral'] = intval($this->dao->getBillSumColumn($where_data + ['pm' => 1]));
		$where_data['type'] = 'sign';
		$data['CountSign'] = $this->dao->getUserSignPoint($where_data);
		$data['SumSign'] = intval($this->dao->getBillSumColumn($where_data));
		unset($where_data['type']);
		$data['SumDeductionIntegral'] = intval($this->dao->getBillSumColumn($where_data + ['pm' => 0]));
        return [
            [
                'col' => 6,
                'count' => $data['SumIntegral'],
                'name' => '总gaga币(个)',
            ],
            [
                'col' => 6,
                'count' => $data['CountSign'],
                'name' => '客户签到次数(次)',
            ],
            [
                'col' => 6,
                'count' => $data['SumSign'],
                'name' => '签到送出gaga币(个)',
            ],
            [
                'col' => 6,
                'count' => $data['SumDeductionIntegral'],
                'name' => '使用gaga币(个)',
            ],
        ];
    }

    /**
     * @param int $uid
     * @param int $type
     * @return array
     */
    public function getUserBillList(int $uid, int $type): array
    {
        $where = [];
        $where['uid'] = $uid;
        $where['category'] = 'now_money';
        switch ((int)$type) {
            case 0:
                $where['type'] = ['recharge', 'pay_money', 'system_add', 'pay_product_refund', 'system_sub', 'pay_member', 'offline_scan', 'lottery_use', 'lottery_add'];
                break;
            case 1:
                $where['type'] = ['pay_money', 'pay_member', 'offline_scan', 'user_recharge_refund', 'lottery_use'];
                break;
            case 2:
                $where['type'] = ['recharge', 'system_add', 'lottery_add'];
                break;
            case 3:
                $where['type'] = ['brokerage', 'brokerage_user'];
                break;
            case 4:
                $where['type'] = ['extract'];
                break;
        }
        $field = 'FROM_UNIXTIME(add_time,"%Y-%m") as time,group_concat(id SEPARATOR ",") ids';
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getUserBillListByGroup($where, $field, 'time', $page, $limit);
        $data = [];
        if ($list) {
            $listIds = array_column($list, 'ids');
            $ids = [];
            foreach ($listIds as $id) {
                $ids = array_merge($ids, explode(',', $id));
            }
            $info = $this->dao->getColumn([['id', 'in', $ids]], 'FROM_UNIXTIME(add_time,"%Y-%m-%d %H:%i") as add_time,title,number,pm', 'id');
            foreach ($list as $item) {
                $value['time'] = $item['time'];
                $id = explode(',', $item['ids']);
                array_multisort($id, SORT_DESC);
                $value['list'] = [];
                foreach ($id as $v) {
                    if (isset($info[$v])) {
                        $value['list'][] = $info[$v];
                    }
                }
                array_push($data, $value);
            }
        }
        return $data;
    }
}
