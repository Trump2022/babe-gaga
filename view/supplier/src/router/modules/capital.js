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

const pre = 'capital_';

export default {
  path: `${Setting.roterPre}/finance/`,
  name: 'capital',
  header: 'capital',
  meta: {
    // 授权标识
    auth: ['supplier-finance']
  },
  redirect: {
    name: `${pre}capital`
  },
  component: BasicLayout,
  children: [
    {
      path: `${Setting.roterPre}/capital/index`,
      name: `${pre}capital`,
      meta: {
        title: '供应商流水',
        auth: ['supplier-capital-index']
      },
      component: () => import('@/pages/capital/index')
    },
    {
      path: `${Setting.roterPre}/bill/index`,
      name: `${pre}bill`,
      meta: {
        title: '账单记录',
        auth: ['supplier-bill-index']
      },
      component: () => import('@/pages/capital/bill')
    },
    {
      path: `${Setting.roterPre}/cash/index`,
      name: `${pre}cash`,
      meta: {
        title: '转账申请',
        auth: ['supplier-cash-index']
      },
      component: () => import('@/pages/capital/cash')
    },
    {
      path: `${Setting.roterPre}/finance/set`,
      name: `${pre}setting`,
      meta: {
        title: '财务设置',
        auth: ['supplier-finance-set']
      },
      component: () => import('@/pages/capital/setting')
    },
  ]
};