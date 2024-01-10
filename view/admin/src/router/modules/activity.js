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

const pre = 'activity_';

export default {
  path: `${Setting.roterPre}/activity`,
  name: 'activity',
  header: 'activity',
  // redirect: {
  //     name: `${pre}article`
  // },
  component: BasicLayout,
  children: [
    {
      path: 'article/index/:id?',
      name: `${pre}article`,
      meta: {
        auth: ['activity-article-index'],
        title: '活动管理',
      },
      component: () => import('@/pages/activity/article/index'),
    },
    {
      path: 'article_category/index',
      name: `${pre}articleCategory`,
      meta: {
        auth: ['activity-article-category'],
        title: '活动分类',
      },
      component: () => import('@/pages/activity/articleCategory/index'),
    },
    {
      path: 'article/add_article/:id?',
      name: `${pre}addArticle`,
      meta: {
        auth: ['activity-article-creat'],
        title: '活动添加',
      },
      component: () => import('@/pages/activity/addArticle/index'),
    },
  ],
};
