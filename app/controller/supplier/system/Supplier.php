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

namespace app\controller\supplier\system;

use app\controller\supplier\AuthController;
use app\services\supplier\SystemSupplierServices;
use think\facade\App;

/**
 * 供应商控制器
 * Class Supplier
 * @package app\controller\supplier
 */
class Supplier extends AuthController
{

    /**
     * 构造方法
     * Supplier constructor.
     * @param App $app
     * @param SystemSupplierServices $supplierServices
     */
    public function __construct(App $app, SystemSupplierServices $supplierServices)
    {
        parent::__construct($app);
        $this->services = $supplierServices;
    }

    /**
     * 获取供应商信息
     * @return void
     */
    public function read()
    {
        $info = $this->services->getSupplierInfo((int)$this->supplierId);
        return $this->success($info->toArray());
    }

    /**
     * 更新供应商信息
     * @return void
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['supplier_name', ''],
            ['name', ''],
            ['phone', ''],
            ['email', ''],
            ['address', ''],
            ['province', 0],
            ['city', 0],
            ['area', 0],
            ['street', 0],
            ['detailed_address', ''],
        ]);

        $this->validate($data, \app\validate\supplier\SystemSupplierValidate::class, 'update');
        $data['address'] = str_replace([' ', '/', '\\'], '', $data['address']);
        $data['detailed_address'] = str_replace([' ', '/', '\\'], '', $data['detailed_address']);
        $this->services->update((int)$this->supplierId, $data);
        return $this->success('保存成功');
    }

    /**
     * 获取供应商财务信息
     * @return mixed
     */
    public function getFinanceInfo()
    {
        $Info = $this->services->get((int)$this->supplierId);
        if (!$Info) {
            return app('json')->fail('供应商不存在');
        }
        return app('json')->success($Info->toArray());
    }

    /**
     * 设置供应商财务信息
     * @return mixed
     */
    public function setFinanceInfo()
    {
        $data = $this->request->postMore([
            ['bank_code', ''],
            ['bank_address', ''],
            ['alipay_account', ''],
            ['alipay_qrcode_url', ''],
            ['wechat', ''],
            ['wechat_qrcode_url', '']
        ]);
        $Info = $this->services->get((int)$this->supplierId);
        if (!$Info) {
            return app('json')->fail('供应商不存在');
        }
        if ($this->services->update($Info['id'], $data)) {
            return app('json')->success('设置成功');
        } else {
            return app('json')->fail('设置失败');
        }
    }
}
