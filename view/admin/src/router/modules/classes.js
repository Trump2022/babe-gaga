/*
 * @Author: 默°
 * @Date: 2024-01-06 15:14:45
 * @LastEditTime: 2024-01-10 10:07:29
 * @LastEditors: 默°
 * @Description: 搬什么砖，摸鱼吧
 * 舒适圈那么舒服为什么要离开
 */
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
import Setting from '@/setting';

const pre = 'classes_';

export default {
  path: `${Setting.roterPre}/classes`,
  name: 'classes',
  header: 'classes',
  meta: {
    // 授权标识
    auth: ['admin-classes'],
  },
  // redirect: {
  //     name: `${pre}productList`
  // },
  component: BasicLayout,
  children: [
    {
      path: 'classes_list',
      name: `${pre}classesList`,
      meta: {
        title: '课程管理',
        keepAlive: true,
        scollTopPosition: 0,
        auth: ['admin-store-storeClasses-index'],
      },
      component: () => import('@/pages/classes/classesList'),
    },
    {
      path: 'add_classes/:id?',
      name: `${pre}classesAdd`,
      meta: {
        auth: ['admin-store-storeClasses-index'],
        title: '课程添加',
      },
      component: () => import('@/pages/classes/classesAdd'),
    },
    {
      path: 'classes_classify',
      name: `${pre}classesClassify`,
      meta: {
        title: '课程分类',
        auth: ['admin-store-classesCategory-index'],
      },
      component: () => import('@/pages/classes/classesClassify'),
    },
    {
      path: 'classes_attr',
      name: `${pre}classesAttr`,
      meta: {
        auth: ['product-classes-attr'],
        title: '课程规格',
      },
      component: () => import('@/pages/classes/classesAttr'),
    },
    {
      path: 'classes_reply/:id?',
      name: `${pre}classesEvaluate`,
      meta: {
        auth: ['product-classes-reply'],
        title: '课程评论',
      },
      component: () => import('@/pages/classes/classesReply'),
    },
  ],
};
