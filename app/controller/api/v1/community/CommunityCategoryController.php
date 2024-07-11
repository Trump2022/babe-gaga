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

namespace app\controller\api\v1\community;

//use app\Request;
//use app\validate\admin\StoreCategoryValidate;
use app\services\community\CommunityCategoryServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\Response;

/**
 * Class CommunityCategoryController
 * @packge app\controller\api\v1\community
 * @date : 2024/1/11 18:17
 * @author: Trump
 */
class CommunityCategoryController
{
    protected ?CommunityCategoryServices $services = NUll;

    /**
     * @param CommunityCategoryServices $services
     */
    public function __construct(CommunityCategoryServices $services)
    {
        $this->services = $services;
        if ( ! sys_config('community_status') ) throw  new ValidateException('未开启社区功能');
    }

    /**
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @descr: I am on my way to future,where you are there.
     * @date : 2024/1/10 17:42
     * @author: Trump
     */
    public function lst(): Response
    {
        return app('json')->success($this->services->getApiList());
    }
}
