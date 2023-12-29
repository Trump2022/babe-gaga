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

namespace app\services\message;


use app\jobs\notice\EnterpriseWechatJob;
use app\jobs\notice\PrintJob;
use app\services\activity\collage\UserCollagePartakeServices;
use app\services\activity\collage\UserCollageServices;
use app\services\BaseServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderServices;
use app\services\system\config\ConfigServices;
use app\services\wechat\WechatUserServices;
use crmeb\services\CacheService;
use think\exception\ValidateException;


class NoticeService extends BaseServices
{

    /**
     * 发送消息类型
     * @var array
     */
//    protected $type = [
//        'is_sms' => NoticeSmsService::class,
//        'is_system' => SystemSendServices::class,
//        'is_wechat' => WechatTemplateService::class,
//        'is_routine' => RoutineTemplateServices::class,
//        'is_ent_wechat' => EntWechatServices::class,
//    ];

    /**
     * @var array
     */
    protected $noticeInfo = [];

    /**
     * @var string
     */
    protected $event;

    /**
     * @param string $event
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setEvent(string $event)
    {
        if ($this->event != $event) {
            $this->noticeInfo = CacheService::redisHandler('NOTCEINFO')->remember('NOTCE_' . $event, function () use ($event) {
                /** @var SystemNotificationServices $services */
                $services = app()->make(SystemNotificationServices::class);
                $noticeInfo = $services->getOneNotce(['mark' => $event]);
				if ($noticeInfo) {
					return $noticeInfo->toArray();
				} else {
					return [];
				}
            });
            $this->event = $event;
        }
        return $this;
    }


    /**
     * @param array $notceinfo
     * @param $data
     * @param string $msgtype
     */
    //企业微信群机器人
    public function EnterpriseWechatSend($data)
    {
        if ($this->noticeInfo['is_ent_wechat'] == 1 && $this->noticeInfo['url'] !== '') {
            $url = $this->noticeInfo['url'];
            $ent_wechat_text = $this->noticeInfo['ent_wechat_text'];
            EnterpriseWechatJob::dispatchDo('doJob', [$data, $url, $ent_wechat_text]);

        }
    }

    /**
     * 根据UID,user_type获取openid
     * @param int $uid
     * @param string $userType
     * @return mixed
     */
    public function getOpenidByUid(int $uid, string $userType = 'wechat')
    {
        /** @var WechatUserServices $wechatServices */
        $wechatServices = app()->make(WechatUserServices::class);
        return $wechatServices->uidToOpenid($uid, $userType);
    }


}
