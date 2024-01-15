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

namespace app\dao\other;


use app\dao\BaseDao;
use app\model\other\Cache;
use think\db\exception\DbException;

/**
 * Class CacheDao
 * @package app\dao\other
 */
class CacheDao extends BaseDao
{

    /**
     * @return string
     */
    public function setModel(): string
    {
        return Cache::class;
    }

    /**
     * 清除过期缓存
     * @throws \Exception
     */
    public function delectDeOverdueDbCache()
    {
        $this->getModel()->where('expire_time', '<>', 0)->where('expire_time', '<', time())->delete();
    }

    /**
     * @param string $key
     * @param $data
     * @throws DbException
     * @author xaboy
     * @day 2020-04-24
     */
    public function keyUpdate(string $key, $data)
    {
        if (isset($data['result']))
            $data['result'] = json_encode($data['result'], JSON_UNESCAPED_UNICODE);
        Cache::getDB()->where('key', $key)->update($data);
    }
}
