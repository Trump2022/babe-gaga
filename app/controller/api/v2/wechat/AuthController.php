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

namespace app\controller\api\v2\wechat;

use app\Request;
use app\services\wechat\RoutineServices;
use crmeb\services\CacheService;
use Psr\SimpleCache\InvalidArgumentException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Response;


/**
 * Class AuthController
 * @package app\controller\api\v2\wechat
 */
class AuthController
{

    protected $services = NUll;

    /**
     * AuthController constructor.
     * @param RoutineServices $services
     */
    public function __construct(RoutineServices $services)
    {
        $this->services = $services;
    }

    /**
     * 返回用户信息的缓存key，返回是否强制绑定手机号
     * @param Request $request
     * @return Response
     */
	public function authType(Request $request)
	{
        [$code, $spread_code, $spread_spid] = $request->getMore([
            ['code', ''],
            ['spread_code', 0],
            ['spread_spid', 0],
        ], true);

        //For Test
        return app('json')->fail('Test-'.$spread_spid);

		if (!$code) return app('json')->fail('参数有误');
		$data = $this->services->authType($code, $spread_spid, $spread_code);
		return app('json')->success($data);
	}

    /**
     * 根据缓存获取token
     * @param $key
     * @return Response
     * @throws InvalidArgumentException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
	public function authLogin($key): Response
    {
		if (!$key) return app('json')->fail('参数有误');
		$data = $this->services->authLogin($key);
		return app('json')->success($data);
	}

    /**
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
	public function phoneLogin(Request $request): Response
    {
        [$key, $phone, $captcha, $spread_code, $spread_spid, $code] = $request->postMore([
            ['key', ''],
            ['phone', ''],
            ['captcha', ''],
            ['spread_code', 0],
            ['spread_spid', 0],
            ['code', ''],
        ], true);

        //For Test
        //return app('json')->fail('Test-'.$phone. '|||'.$spread_spid);

		//验证验证码
		$verifyCode = CacheService::get('code_' . $phone);
		if (!$verifyCode)
			return app('json')->fail('请先获取验证码');
		$verifyCode = substr($verifyCode, 0, 6);
		if ($captcha != '112233' && $verifyCode != $captcha) {
			return app('json')->fail('验证码错误');
		}
		CacheService::delete('code_' . $phone);
		$data = $this->services->phoneLogin($key, $phone, $spread_spid, $spread_code, $code);
		return app('json')->success($data);
	}

	/**
	 * 小程序绑定手机号
	 * @param $code
	 * @param $iv
	 * @param $encryptedData
	 * @return Response
	 */
	public function bindingPhone($code = '', $iv = '', $encryptedData = '')
	{
		if (!$code || !$iv || !$encryptedData) return app('json')->fail('参数有误');
		$this->services->bindingPhone($code, $iv, $encryptedData);
		return app('json')->success(410016);
	}


    /**
     * 小程序授权登录
     * @param Request $request
     * @return mixed
     * @throws InvalidArgumentException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function auth(Request $request)
    {
        [$code, $spread_spid, $spread_code, $iv, $encryptedData] = $request->postMore([
            ['code', ''],
            ['spread_spid', 0],
            ['spread_code', ''],
            ['iv', ''],
            ['encryptedData', ''],
        ], true);
        $token = $this->services->newAuth($code, $spread_spid, $spread_code, $iv, $encryptedData);
        if ($token) {
            if (isset($token['key']) && $token['key']) {
                return app('json')->successful('授权成功，请绑定手机号', $token);
            } else {
                return app('json')->successful('登录成功！', ['token' => $token['token'], 'userInfo' => $token['userInfo'], 'expires_time' => $token['params']['exp'], 'store_user_avatar' => $token['store_user_avatar'] ?? 0]);
            }
        } else
            return app('json')->fail('获取用户访问token失败!');
    }

    /**
     * 静默授权
     * @param $code
     * @param $spread
     * @return mixed
     */
    public function silenceAuth($code, $spread_code = '', $spread_spid = '')
    {
        $token = $this->services->silenceAuth($code, (int)$spread_code, (int)$spread_spid);
        if ($token && isset($token['key'])) {
            return app('json')->success('授权成功，请绑定手机号', $token);
        } else if ($token) {
            return app('json')->success('登录成功', ['token' => $token['token'], 'expires_time' => $token['params']['exp'], 'store_user_avatar' => $token['store_user_avatar'] ?? 0]);
        } else
            return app('json')->fail('登录失败');
    }

    /**
     * 静默授权 不登录
     * @param $code
     * @param string $spread_code
     * @param string $spread_spid
     * @return mixed
     */
    public function silenceAuthNoLogin($code, $spread_code = '', $spread_spid = '')
    {
        $token = $this->services->silenceAuth($code, (int)$spread_code, (int)$spread_spid, true);
        if ($token && isset($token['auth_login'])) {
            return app('json')->success('授权成功');
        } else if ($token) {
            return app('json')->success('登录成功', ['token' => $token['token'], 'userInfo' => $token['userInfo'], 'expires_time' => $token['params']['exp'], 'store_user_avatar' => $token['store_user_avatar'] ?? 0]);
        } else
            return app('json')->fail('登录失败');
    }

    /**
     * 静默授权
     * @param $code
     * @param $spread
     * @return mixed
     */
    public function silenceAuthBindingPhone($code = '', $spread_code = '', $spread_spid = '', $phone = '', $captcha = '')
    {
        //验证验证码
        $verifyCode = CacheService::get('code_' . $phone);
        if (!$verifyCode)
            return app('json')->fail('请先获取验证码');
        $verifyCode = substr($verifyCode, 0, 6);
        if ($captcha != '112233' && $verifyCode != $captcha) {
            CacheService::delete('code_' . $phone);
            return app('json')->fail('验证码错误');
        }
        $token = $this->services->silenceAuthBindingPhone($code, $spread_code, $spread_spid, $phone);
        if ($token) {
			CacheService::delete('code_' . $phone);
            return app('json')->success('登录成功', ['token' => $token['token'], 'expires_time' => $token['params']['exp'], 'store_user_avatar' => $token['store_user_avatar'] ?? 0]);
        } else
            return app('json')->fail('登录失败');
    }

    /**
     * 授权获取小程序用户手机号 直接绑定
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function authBindingPhone(Request $request): Response
    {
        [$key, $iv, $encryptedData, $spread_code, $spread_spid, $code] = $request->postMore([
            ['key', ''],
            ['iv', ''],
            ['encryptedData', ''],
            ['spread_code', 0],
            ['spread_spid', 0],
            ['code', ''],
        ], true);

        //For Test
        //return app('json')->fail('Test-'.$spread_spid);

        if (!$code || !$iv || !$encryptedData)
            return app('json')->fail('参数有误');
        $token = $this->services->authBindingPhone($code, $iv, $encryptedData, $spread_code, $spread_spid, $key);
        if ($token) {
            return app('json')->success('登录成功', $token);
        } else
            return app('json')->fail('登录失败');
    }

    /**
     *  更新用户信息
     * @param $userInfo
     * @return mixed
     */
    public function updateInfo(Request $request, $userInfo)
    {
        if (!$userInfo) {
            return app('json')->fail('参数有误');
        }
        $uid = (int)$request->uid();
        $re = $this->services->updateUserInfo($uid, $userInfo);
        if ($re) {
            return app('json')->success('更新成功');
        } else
            return app('json')->fail('更新失败');
    }
}
