// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import BasicLayout from '@/layouts/basic-layout';
import Setting from "@/setting";

const pre = 'marketing_';

export default {
    path: `${Setting.routePre}/marketing/`,
    name: 'marketing',
    header: 'marketing',
    meta: {
        // 授权标识
        auth: ['store-marketing']
    },
    redirect: {
        name: `${pre}shortVideo`
    },
    component: BasicLayout,
    children: [
        {
            path: 'short_video/index',
            name: `${pre}shortVideo`,
            meta: {
                auth: ['store-marketing-short_video-index'],
                title: '短视频'
            },
            component: () => import('@/pages/marketing/shortVideo/index')
        },
        {
            path: 'short_video/create/:id?',
            name: `${pre}shortVideoCreate`,
            meta: {
                auth: ['store-marketing-short_video-create'],
                title: '短视频添加'
            },
            component: () => import('@/pages/marketing/shortVideo/create')
        },
        {
            path: 'short_video/comment',
            name: `${pre}comment`,
            meta: {
                auth: ['store-marketing-short_video-comment'],
                title: '短视频评论'
            },
            component: () => import('@/pages/marketing/shortVideo/comment')
        },
    ]
};
