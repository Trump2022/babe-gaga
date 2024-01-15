<?php

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserBrokerages;

class UserBrokeragesDao extends BaseDao
{
    protected function setModel(): string
    {
        return UserBrokerages::class;
    }

    public function search(array $where = [])
    {
        return UserBrokerages::getDB()->when(isset($where['brokerage_name']) && $where['brokerage_name'] !== '', function ($query) use ($where) {
            $query->whereLike('brokerage_name', "%{$where['brokerage_name']}%");
        })->when(isset($where['brokerage_level']) && $where['brokerage_level'] !== '', function ($query) use ($where) {
            $query->where('brokerage_level', $where['brokerage_level']);
        })->when(isset($where['next_level']) && $where['next_level'] !== '', function ($query) use ($where) {
            $query->where('brokerage_level', '>', $where['next_level']);
        })->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where) {
            $query->where('type', $where['type']);
        });
    }

    public function fieldExists($field, $value, ?int $except = null, int $type = 0): bool
    {
        $query = ($this->getModel())::getDB()->where('type',$type)->where($field, $value);
        if (!is_null($except)) $query->where($this->getPk(), '<>', $except);
        return $query->count() > 0;
    }
}