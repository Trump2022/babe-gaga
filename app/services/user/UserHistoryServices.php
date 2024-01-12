<?php

namespace app\services\user;

use app\dao\user\UserHistoryDao;
use app\services\BaseServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class UserHistoryServices
 * @packge app\services\user
 * @date : 2024/1/12 16:10
 * @author: QSW
 * @email: qsw2012@outlook.com
 * @mixin UserHistoryDao
 */
class UserHistoryServices extends BaseServices
{
//    protected $dao;

    /**
     * UserHistoryServices constructor.
     * @param UserHistoryDao $dao
     */
    public function __construct(UserHistoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 16:12
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function historyLst($where): array
    {
        [$page, $limit] = $this->getPageValue();
        $query = $this->dao->joinSpu($where);
        $query->with([
            'spu'
        ])->order('update_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)
            ->setOption('field',[])->field('uid,product_id,product_type,spu_id,image,store_name,price')
            ->select();
        return compact('count','list');
    }
}