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
namespace app\controller\api\v1\system;

use app\Request;
use app\services\supplier\SystemSupplierServices;
use app\services\system\SystemUserApplyServices;
use app\validate\api\user\UserApplyValidate;
use crmeb\services\CacheService;
use think\exception\ValidateException;


/**
 * 用户申请类
 * Class UserApplyController
 * @package app\controller\api\v1\system
 */
class UserApplyController
{
    protected $services = NUll;

    /**
     * UserExtractController constructor.
     * @param SystemUserApplyServices $services
     */
    public function __construct(SystemUserApplyServices $services)
    {
        $this->services = $services;
    }

	/**
	 * 获取单个申请详情
	 * @param Request $request
	 * @param SystemSupplierServices $supplierServices
	 * @param $id
	 * @return \think\Response
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function getInfo(Request $request, SystemSupplierServices $supplierServices, $id)
	{
		if (!$id) {
			return app('json')->fail('缺少参数');
		}
		$info = $this->services->get((int)$id);
		if (!$info || $info['uid'] != $request->uid()) {
			return app('json')->fail('数据不存在');
		}
		$info = $info->toArray();
		$data = ['url' => sys_config('site_url') . '/'. config('admin.supplier_prefix'), 'account' => '', 'pwd' => ''];
		if ($info['status'] == 1) {//审核通过
			$data['account'] = $supplierServices->getAccount($info['phone']);
			$data['pwd'] = substr($info['phone'], -6);
		}
		$info = array_merge($info, $data);
		return app('json')->success($info);
	}


	/**
	 * 申请供应商
	 * @param Request $request
	 * @param $id
	 * @return \think\Response
	 */
    public function userApply(Request $request, $id)
    {
        $data = $request->postMore([
			['name', ''],
            ['phone', ''],
            ['system_name', ''],
			['captcha', ''],
            ['images', []],
        ]);
		//验证手机号
		try {
			validate(UserApplyValidate::class)->check($data);
		} catch (ValidateException $e) {
			return app('json')->fail($e->getError());
		}
		$captcha = $data['captcha'];
		unset($data['captcha']);
		//验证验证码
		$verifyCode = CacheService::get('code_' . $data['phone']);
		if (!$verifyCode)
			return app('json')->fail('请先获取验证码');
		$verifyCode = substr($verifyCode, 0, 6);
		if ($verifyCode != $captcha) {
			return app('json')->fail('验证码错误');
		}
		$res = $this->services->saveApply((int)$id, (int)$request->uid(), $data);
		if ($res) {
			CacheService::delete('code_' . $data['phone']);
			return app('json')->successful('申请成功!', ['id' => $res]);
		}
		return app('json')->fail('申请失败!');
    }

	/**
	 * 获取申请记录
	 * @param Request $request
	 * @return \think\Response
	 */
	public function userApplyRecord(Request $request)
	{
		return app('json')->success($this->services->getUserApply((int)$request->uid()));
	}

}
