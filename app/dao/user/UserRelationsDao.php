<?php

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserRelation;
use app\model\user\UserRelations;
use think\db\BaseQuery;

class UserRelationsDao extends BaseDao
{
    protected function setModel(): string
    {
        return UserRelations::class;
    }

    /**
     * @param string|null $keyword
     * @param int $uid
     * @return BaseQuery
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/12 15:24
     * @author: Trump
     */
    public function getUserProductToCommunity(?string  $keyword, int $uid): BaseQuery
    {
        $query = UserRelations::hasWhere('spu', function ($query) use($keyword) {
            $query->when($keyword, function ($query) use($keyword) {
                $query->whereLike('store_name',"%{$keyword}%");
            });
            $query->where('status',1);
        });
        $query->where('uid',$uid);
        return $query;
    }
}