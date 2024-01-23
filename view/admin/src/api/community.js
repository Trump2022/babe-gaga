// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import request from '@/plugins/request';

/**
 * @description 社区分类 -- 列表
 */
export function communityCategoryListApi(data) {
  return request({
    url: 'community/category/lst',
    method: 'get',
    params: data,
  });
}
/**
 * @description 社区分类 -- 新增表单
 */
export function communityCategoryCreateApi() {
  return request({
    url: 'community/category/create/form',
    method: 'get',
  });
}
/**
 * @description 社区分类 -- 编辑表单
 */
export function communityCategoryUpdateApi(id) {
  return request({
    url: `community/category/edit/form/${id}`,
    method: 'get',
  });
}
/**
 * @description 社区分类 -- 删除
 */
export function communityCategoryDeleteApi(id) {
  return request({
    url: `community/category/delete/${id}`,
    method: 'delete',
  });
}
/**
 * @description 社区分类 -- 修改状态
 */
export function communityCategoryStatusApi(id, status) {
  return request({
    url: `community/category/status/${id}`,
    method: 'post',
    params: { status },
  });
}
/**
 * @description 社区话题 -- 列表
 */
export function communityTopicListApi(data) {
  return request({
    url: 'community/topic/lst',
    method: 'get',
    params: data,
  });
}
/**
 * @description 社区话题 -- 新增表单
 */
export function communityTopicCreateApi() {
  return request({
    url: 'community/topic/create/form',
    method: 'get',
  });
}
/**
 * @description 社区话题 -- 编辑表单
 */
export function communityTopicUpdateApi(id) {
  return request({
    url: `community/topic/edit/form/${id}`,
    method: 'get',
  });
}
/**
 * @description 社区话题 -- 删除
 */
export function communityTopicDeleteApi(id) {
  return request({
    url: `community/topic/delete/${id}`,
    method: 'delete',
  });
}
/**
 * @description 社区话题 -- 修改状态
 */
export function communityTopicStatusApi(id, status) {
  return request({
    url: `community/topic/status/${id}`,
    method: 'post',
    data: { status },
  });
}
/**
 * @description 社区话题 -- 修改状态
 */
export function communityTopicHotApi(id, status) {
  return request({
    url: `community/topic/hot/${id}`,
    method: 'post',
    data: { status },
  });
}
/**
 * @description 社区文章 -- 列表
 */
export function communityListApi(data) {
  return request({
    url: 'community/community/lst',
    method: 'get',
    params: data,
  });
}
/**
 * @description 社区文章 -- 详情
 */
export function communityDetailApi(id) {
  return request({
    url: `community/community/detail/${id}`,
    method: 'get',
  });
}
/**
 * @description 社区文章 -- 审核/下架
 */
export function communityAuditApi(id, data) {
  return request({
    url: `community/community/status/${id}`,
    method: 'post',
    params: { data },
  });
}
/**
 * @description 社区文章 -- 编辑星级
 */
export function communityUpdateApi(id) {
  return request({
    url: `community/community/edit/form/${id}`,
    method: 'get',
  });
}
/**
 * @description 社区文章 -- 编辑状态
 */
export function communityStatusApi(id, status) {
  return request({
    url: `community/community/show/${id}`,
    method: 'post',
    data: { status },
  });
}
/**
 * @description 社区文章 -- 是否推荐
 */
export function communityHotApi(id) {
  return request({
    url: `community/hot/${id}`,
    method: 'post',
  });
}

/**
 * @description 社区文章 -- 删除
 */
export function communityDeleteApi(id) {
  return request({
    url: `community/delete/${id}`,
    method: 'delete',
  });
}
/**
 * @description 社区文章 -- 强制下架
 */

export function communityOffApi(id) {
  return request({
    url: `community/community/status/form/${id}`,
    method: 'get',
  });
}
/**
 * @description 社区文章 -- 分类筛选
 */

export function communityCateOptionApi() {
  return request({
    url: 'community/category/option',
    method: 'get',
  });
}
/**
 * @description 社区文章 -- 话题筛选
 */

export function communityTopicOptionApi() {
  return request({
    url: 'community/topic/option',
    method: 'get',
  });
}
/**
 * @description 社区评论 -- 列表
 */
export function communityReplyListApi(data) {
  return request({
    url: 'community/reply/lst',
    method: 'get',
    params: data,
  });
}
/**
 * @description 社区评论 -- 删除
 */
export function communityReplyDeleteApi(id) {
  return request({
    url: `community/reply/delete/${id}`,
    method: 'delete',
  });
}
/**
 * @description 社区评论 -- 审核
 */
export function communityReviewApi(id) {
  return request({
    url: `community/reply/status/form/${id}`,
    method: 'get',
  });
}
/**
 * @description 社区内容 -- 标题切换
 */
export function communityTitleApi() {
  return request({
    url: 'community/community/title',
    method: 'get',
  });
}
