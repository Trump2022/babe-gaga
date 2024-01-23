// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import Layout from '@/layouts/basic-layout';
import Setting from '@/setting';
const pre = 'community_';
export default {
  path: `${Setting.roterPre}/community`,
  name: 'community',
  header: 'community',
  meta: {
    auth: ['admin-community'],
  },
  component: Layout,
  children: [
    {
      path: 'category',
      name: `${pre}communityClassify`,
      meta: {
        title: '社区分类',
        keepAlive: true,
        auth: ['admin-communityCategory-index'],
      },
      component: () => import('@/pages/community/communityClassify'),
    },
    {
      path: 'topic',
      name: `${pre}communityTopic`,
      meta: {
        title: '社区话题',
        keepAlive: true,
        auth: ['admin-communitySubject-index'],
      },
      component: () => import('@/pages/community/communityTopic'),
    },
    {
      path: 'list',
      name: `${pre}communityList`,
      meta: {
        title: '社区内容',
        keepAlive: true,
        scollTopPosition: 0,
        auth: ['admin-community-index'],
      },
      component: () => import('@/pages/community/communityList'),
    },
    {
      path: 'reply',
      name: `${pre}communityReply`,
      meta: {
        title: '社区评论',
        keepAlive: true,
        auth: ['admin-community-reply'],
      },
      component: () => import('@/pages/community/communityComment'),
    },
  ],
};
