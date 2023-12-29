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

const pre = 'set_';

export default {
	path: `${Setting.routePre}/set/`,
	name: 'set',
	header: 'set',
	meta: {
		// 授权标识
		auth: ['store-set']
	},
	redirect: {
		name: `${pre}set`
	},
	component: BasicLayout,
	children: [{
			path: 'store',
			name: `${pre}set`,
			meta: {
				title: '门店设置',
				auth: ['store-set-store']
			},
			component: () => import('@/pages/setting/index')
		},
		{
			path: 'index',
			name: `${pre}setting`,
			meta: {
				title: '系统设置',
				auth: ['store-set-index']
			},
			props: {
			    typeMole: 'third'
			},
			component: () => import('@/components/fromSubmit/commonForm.vue')
		},
		{
			path: 'delivery/record',
			name: `${pre}setting`,
			meta: {
				title: '配送记录',
				auth: ['store-set-delivery-record']
			},
			component: () => import('@/pages/setting/deliveryRecord/index')
		},
		{
			path: `${Setting.routePre}/admin/index`,
			name: `${pre}admin`,
			meta: {
				title: '管理员列表',
				auth: ['store-admin-index']
			},
			component: () => import('@/pages/setting/admin/index')
		},
    {
			path: `${Setting.routePre}/set/table_code/index`,
			name: `${pre}table_code`,
			meta: {
				title: '桌码设置',
				auth: ['store-set-table_code-index']
			},
			component: () => import('@/pages/setting/tableCode/index')
		},
    {
			path: `${Setting.routePre}/set/table_code/classify`,
			name: `${pre}tableCodeClassify`,
			meta: {
				title: '桌码分类',
				auth: ['store-set-table_code-classify']
			},
			component: () => import('@/pages/setting/tableCode/classify')
		},
    {
			path: `${Setting.routePre}/set/table_code/list`,
			name: `${pre}tableCodeList`,
			meta: {
				title: '桌码列表',
				auth: ['store-set-table_code-list']
			},
			component: () => import('@/pages/setting/tableCode/list')
		}
	]
};
