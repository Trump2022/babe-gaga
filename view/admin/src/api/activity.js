// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import request from '@/plugins/request';

/**
 * @description 文章管理--列表
 * @param {Object} param data {Object} 传值参数
 */
export function activityListApi(data) {
  return request({
    url: 'activity/activity',
    method: 'get',
    params: data,
  });
}

/**
 * @description 文章管理--新增编辑
 * @param {Object} param data {Object} 传值参数
 */
export function activityAddApi(data) {
  return request({
    url: 'activity/activity',
    method: 'post',
    data,
  });
}

/**
 * @description 文章管理--文章详情
 * @param {Number} param id {Number} 文章id
 */
export function createApi(id) {
  return request({
    url: `activity/activity/${id}`,
    method: 'get',
  });
}

/**
 * @description 文章分类--新增表单
 */
export function categoryAddApi() {
  return request({
    url: `activity/category/create`,
    method: 'GET',
  });
}

/**
 * @description 文章分类--列表
 * @param {Object} param params {Object} 传值
 */
export function categoryListApi(params) {
  return request({
    url: `activity/category`,
    method: 'GET',
    params,
  });
}

/**
 * @description 文章分类--编辑表单
 * @param {Number} param id {Number} 文章id
 */
export function categoryEditApi(id) {
  return request({
    url: `activity/category/${id}/edit`,
    method: 'GET',
  });
}

/**
 * @description 文章分类--修改状态
 * @param {Object} param data {Object} 传值
 */
export function statusApi(data) {
  return request({
    url: `activity/category/set_status/${data.id}/${data.status}`,
    method: 'put',
  });
}

/**
 * @description 文章分类--关联商品
 * @param {Number} param id {Number} 文章id
 * @param {Object} param data {Object} 传值
 */
export function relationApi(data, id) {
  return request({
    url: `activity/activity/relation/${id}`,
    method: 'put',
    data,
  });
}
