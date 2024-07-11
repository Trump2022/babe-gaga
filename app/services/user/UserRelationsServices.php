<?php

namespace app\services\user;

use app\dao\user\UserRelationsDao;
use app\services\BaseServices;

/**
 * Class UserRelationsServices
 * @packge app\services\user
 * @date : 2024/1/12 15:54
 * @author: Trump
 * @mixin UserRelationsDao
 */
class UserRelationsServices extends BaseServices
{
    public function __construct(UserRelationsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param string|null $keyword
     * @param int $uid
     * @return array
     * @author Qinii
     * @day 10/28/21
     */
    public function getUserProductToCommunity(?string $keyword, int $uid): array
    {
        [$page, $limit] = $this->getPageValue();
        $query = $this->dao->getUserProductToCommunity($keyword, $uid)->group('product_id');
        $count = $query->count();
        $list = $query->setOption('field',[])->field('uid,product_id,product_type,spu_id,image,store_name,price')
            ->page($page, $limit)->select();
        return compact('count', 'list');
    }
}