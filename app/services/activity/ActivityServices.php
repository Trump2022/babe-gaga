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

namespace app\services\activity;

use app\dao\activity\ActivityDao;
use app\services\BaseServices;
use app\services\wechat\WechatNewsCategoryServices;
use crmeb\exceptions\AdminException;

/**
 * 定制活动
 * Class ActivityServices
 * @package app\services\activity
 * @mixin ActivityDao
 */
class ActivityServices extends BaseServices
{
    /**
     * ActivityServices constructor.
     * @param ActivityDao $dao
     */
    public function __construct(ActivityDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取列表
     * @param array $where
     * @return array
     */
    public function getList(array $where, int $page = 0, int $limit = 0)
    {
        if (!$limit) {
            [$page, $limit] = $this->getPageValue();
        }
        /** @var WechatNewsCategoryServices $services */
        $services = app()->make(WechatNewsCategoryServices::class);
        $where['ids'] = $services->getNewIds();
        $list = $this->dao->getList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['store_name'] = $item['storeInfo']['store_name'] ?? '';
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 新增编辑定制活动
     * @param array $data
     */
    public function save(array $data)
    {
        /** @var ActivityContentServices $activityContentService */
        $activityContentService = app()->make(ActivityContentServices::class);
        $content['content'] = $data['content'];
        $id = $data['id'];
        unset($data['content'], $data['id']);
        $info = $this->transaction(function () use ($id, $data, $activityContentService, $content) {
            if ($id) {
                $info = $this->dao->update($id, $data);
                $content['nid'] = $id;
                $res = $info && $activityContentService->update($id, $content, 'nid');
            } else {
                unset($data['id']);
                $data['add_time'] = time();
                $info = $this->dao->save($data);
                $content['nid'] = $info->id;
                $res = $info && $activityContentService->save($content);
            }
            if (!$res) {
                throw new AdminException('保存失败');
            } else {
                return $info;
            }
        });
        return $info;
    }

    /**
     * 获取商品详情
     * @param $id
     * @return array
     */
    public function read(int $id)
    {
        $info = $this->dao->read($id);
        $info['cid'] = (int)$info['cid'];
        return compact('info');
    }

    /**
     * 删除商品
     * @param int $id
     */
    public function del(int $id)
    {
        /** @var ActivityContentServices $activityContentService */
        $activityContentService = app()->make(ActivityContentServices::class);
        $this->transaction(function () use ($id, $activityContentService) {
            $res = $this->dao->delete($id);
            $res = $res && $activityContentService->del($id);
            if (!$res) {
                throw new AdminException('删除失败');
            }
        });
    }

    /**
     * 定制活动关联商品
     * @param int $id
     * @param int $product_id
     * @return mixed
     */
    public function bindProduct(int $id, int $product_id = 0)
    {
        return $this->dao->update($id, ['product_id' => $product_id]);
    }

    /**
     * 获取数量
     * @param array $where
     * @return int
     */
    public function count(array $where)
    {
        return $this->dao->count($where);
    }

    /**
     * 获取一条数据
     * @param int $id
     * @return mixed
     */
    public function getInfo(int $id)
    {
        $info = $this->dao->read($id);
        if (!$info) {
            throw new AdminException('定制活动不存在或已删除');
        }
        $info->visit = ($info->visit ?? 0) + 1;
        if (!$info->save())
            throw new AdminException('请稍后查看');
        if ($info) {
            $info = $info->toArray();
            $info['visit'] = (int)$info['visit'];
            $info['add_time'] = date('Y-m-d', $info['add_time']);
        }
        return $info;
    }

    /**
     * 获取定制活动列表
     * @param $new_id
     * @return int
     */
    public function activityList($new_id)
    {
        return $this->dao->activityLists($new_id);
    }

    /**图文详情
     * @param $new_id
     * @return mixed
     */
    public function activitysList($new_id)
    {
        return $this->dao->activityContentList($new_id);
    }
}
