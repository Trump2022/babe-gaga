<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------


namespace app\dao\community;


use app\model\system\Relevance;
use app\services\system\RelevanceServices;
use app\dao\BaseDao;
use app\model\community\Community;
use crmeb\basic\BaseModel;
use think\db\BaseQuery;

class CommunityDao extends BaseDao
{
    /**
     * @return string
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/8 10:16
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    protected function setModel(): string
    {
        return Community::class;
    }

    /**
     * @return BaseModel
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/17 20:10
     * @author: QSW
     * @email: qsw2012@outlook.com
     */
    public function getModel(): BaseModel
    {
        return parent::getModel();
    }

    /**
     * @param array $where
     * @return BaseQuery
     */
    public function search(array $where = []): BaseQuery
    {
        $query = Community::hasWhere('author', function($query) use ($where){
            $query->when(isset($where['username']) && $where['username'] !==  '', function ($query) use($where) {
                $query->whereLike('real_name|phone|nickname',"%{$where['username']}%");
            });
            $query->where(true);
        });
        $query->when(isset($where['uid']) && $where['uid'] !==  '', function ($query) use($where) {
                $query->where('Community.uid',$where['uid']);
            })

            ->when(isset($where['keyword']) && $where['keyword'] !==  '', function ($query) use($where) {
                $query->where(function ($query) use ($where) {
                    $query->where(function ($query) use ($where){
                        $query->whereLike('Community.title|Community.content',"%{$where['keyword']}%");
                    })
                    ->whereOr(function ($query) use ($where) {
                        $query->whereIn('Community.community_id', function ($que) use ($where){
                            $que->name('relevance')->whereIn('right_id', function ($q) use ($where) {
                                $q->name('store_product')->whereLike('store_name', "%{$where['keyword']}%")->field('id')->select();
                            })->where('type', RelevanceServices::TYPE_COMMUNITY_PRODUCT)->field('left_id')->select();
                        });
                    })
                    ;
                });
            })

            ->when(isset($where['uids']) && $where['uids'] !==  '', function ($query) use($where) {
                $query->whereIn('Community.uid',$where['uids']);
            })
            ->when(isset($where['topic_id']) && $where['topic_id'] !==  '', function ($query) use($where) {
                $query->where('Community.topic_id',$where['topic_id']);
            })
            ->when(isset($where['community_id']) && $where['community_id'] !==  '', function ($query) use($where) {
                $query->where('Community.community_id',$where['community_id']);
            })
            ->when(isset($where['not_id']) && $where['not_id'] !==  '', function ($query) use($where) {
                $query->whereNotIn('Community.community_id',$where['not_id']);
            })
            ->when(isset($where['in_id']) && $where['in_id'] !==  '', function ($query) use($where) {
                $query->whereOr(function($query) use($where){
                    $query->whereIn('Community.community_id',$where['in_id']);
                });
            })
            ->when(isset($where['community_ids']) && $where['community_ids'] !==  '', function ($query) use($where) {
                $query->whereIn('Community.community_id',$where['community_ids']);
            })
            ->when(isset($where['is_type']) && $where['is_type'] !==  '', function ($query) use($where) {
                $query->whereIn('Community.is_type',$where['is_type']);
            })
            ->when(isset($where['is_show']) && $where['is_show'] !==  '', function ($query) use($where) {
                $query->where('Community.is_show',$where['is_show']);
            })
            ->when(isset($where['status']) && $where['status'] !==  '', function ($query) use($where) {
                $query->where('Community.status',$where['status']);
            })
            ->when(isset($where['start']) && $where['start'] !==  '', function ($query) use($where) {
                $query->where('Community.start',$where['start']);
            })
            ->when(isset($where['is_del']) && $where['is_del'] !==  '', function ($query) use($where) {
                $query->where('Community.is_del',$where['is_del']);
            })
            ->when(isset($where['category_id']) && $where['category_id'] !==  '', function ($query) use($where) {
                $query->where('Community.category_id',$where['category_id']);
            })
            ->when(isset($where['spu_id']) && $where['spu_id'] !==  '', function ($query) use($where) {
                $id = Relevance::where('right_id', $where['spu_id'])
                    ->where('type',RelevanceServices::TYPE_COMMUNITY_PRODUCT)
                    ->column('left_id');
                $query->where('community_id','in', $id);
            });

        $order = 'Community.create_time DESC';

        if (isset($where['order']) && $where['order'] == 'start') {
            $order = 'Community.start DESC,Community.create_time DESC';
        }

        $query->order($order);
        return $query;
    }

    public function uidExists(int $id, int $uid)
    {
        return $this->getModel()::getDb()->where('uid',$uid)->where($this->getPk(),$id)->count() > 0;
    }

    public function exists(int $id)
    {
        return $this->getModel()::getDb()->where('is_del',0)->where($this->getPk(),$id)->count() > 0;
    }

    public function destoryByUid($uid)
    {
        return $this->getModel()::getDb()->where('uid' ,$uid)->update(['is_del' =>  1]);
    }

    public function joinUser($where)
    {
        return Community::when(isset($where['is_type']) && $where['is_type'] !==  '', function ($query) use($where) {
                $query->whereIn('Community.is_type',$where['is_type']);
            })
            ->when(isset($where['is_show']) && $where['is_show'] !==  '', function ($query) use($where) {
                $query->where('Community.is_show',$where['is_show']);
            })
            ->when(isset($where['status']) && $where['status'] !==  '', function ($query) use($where) {
                $query->where('Community.status',$where['status']);
            })
            ->when(isset($where['is_del']) && $where['is_del'] !==  '', function ($query) use($where) {
                $query->where('Community.is_del',$where['is_del']);
            });
    }
}
