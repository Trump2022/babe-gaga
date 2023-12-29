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
namespace app\controller\supplier\finance;


use app\services\supplier\finance\SupplierTransactionsServices;
use think\facade\App;
use app\controller\supplier\AuthController;


/**
 * 供应商交易
 * Class SupplierTransactions
 * @package app\controller\supplier\finance
 */
class SupplierTransactions extends AuthController
{
    /**
     * StoreFinanceFlow constructor.
     * @param App $app
     * @param SupplierTransactionsServices $services
     */
    public function __construct(App $app, SupplierTransactionsServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

}
