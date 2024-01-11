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

/*
 * 获取商品表单头数量；
 * */
export function getGoodHeade(data) {
  return request({
    url: 'classes/classes/type_header',
    method: 'get',
    params: data,
  });
}

/*
 * 获取商品表单头数量；
 * */
export function getGoodsCategory(data) {
  return request({
    url: '/goods/goods_category',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品管理-- 列表
 */
export function getGoods(params) {
  return request({
    url: 'classes/classes',
    method: 'get',
    params,
  });
}

/**
 * @description 商品管理-- 上下架
 */
export function PostgoodsIsShow(id, isShow) {
  return request({
    url: `classes/classes/set_show/${id}/${isShow}`,
    method: 'put',
  });
}

/**
 * @description 商品属性 -- 批量上下架
 * @param {Object} param data {Object} 传值对象
 */
export function classesShowApi(data) {
  return request({
    url: `classes/classes/classes_show`,
    method: 'put',
    data,
  });
}

/**
 * @description 商品属性 -- 批量下架
 * @param {Object} param data {Object} 传值对象
 */
export function classesUnshowApi(data) {
  return request({
    url: `classes/classes/classes_unshow`,
    method: 'put',
    data,
  });
}

/**
 * @description 商品列表 -- 批量添加配送方式
 * @param {Object} param data {Object} 传值对象
 */
export function setDeliveryType(data) {
  return request({
    url: `classes/classes/setDeliveryType`,
    method: 'put',
    data,
  });
}

/**
 * @description 商品管理-- 审核
 */
export function classesReviewApi(id) {
  return request({
    url: `/classes/classes/verify/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品管理-- 强制下架
 */
export function forcedRemovalApi(id) {
  return request({
    url: `/classes/classes/remove/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品管理-- 分类
 */
export function treeListApi(type) {
  return request({
    url: `classes/category/tree/${type}`,
    method: 'get',
  });
}

/**
 * @description 商品管理-- 详情
 */
export function classesInfoApi(id) {
  return request({
    url: `classes/classes/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品管理-- 提交
 */
export function classesAddApi(data) {
  return request({
    url: `classes/classes/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description 商品分类 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function classesListApi(params) {
  return request({
    url: 'classes/category',
    method: 'get',
    params,
  });
}

/**
 * @description 商品分类 -- 添加表单
 * @param {Object} param params {Object} 传值参数
 */
export function classesCreateApi() {
  return request({
    url: 'classes/category/create',
    method: 'get',
  });
}

/**
 * @description 商品分类 -- 编辑表单
 * @param {Object} param params {Object} 传值参数
 */
export function classesEditApi(id) {
  return request({
    url: `classes/category/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品分类 -- 修改状态
 * @param {Object} param params {Object} 传值参数
 */
export function setShowApi(data) {
  return request({
    url: `classes/category/set_show/${data.id}/${data.is_show}`,
    method: 'PUT',
  });
}

/**
 * @description 选择商品 -- 列表
 */
export function changeListApi(params) {
  return request({
    url: `classes/classes/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description 选择商品 -- 列表-标签
 */
export function allLabelApi(params) {
  return request({
    url: `classes/all_label`,
    method: 'get',
    params,
  });
}

/**
 * @description 商品评论 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function replyListApi(params) {
  return request({
    url: `classes/reply`,
    method: 'get',
    params,
  });
}

/**
 * @description 商品评论 -- 回复
 * @param {Object} param data {Object} 传值参数
 */
export function setReplyApi(data, id) {
  return request({
    url: `classes/reply/set_reply/${id}`,
    method: 'PUT',
    data,
  });
}

/**
 * @description 获取复制商品配置
 */
export function copyConfigApi() {
  return request({
    url: `classes/copy_config`,
    method: 'get',
  });
}

/**
 * @description 商品管理 -- 获取京东、淘宝商品数据
 * @param {Object} param data {Object} 传值参数
 */
export function crawlFromApi(data) {
  return request({
    url: `classes/copy`,
    method: 'POST',
    data,
  });
}

/**
 * @description 商品管理 -- 京东、淘宝商品数据提交
 * @param {Object} param data {Object} 传值参数
 */
export function crawlSaveApi(data) {
  return request({
    url: `classes/crawl/save`,
    method: 'POST',
    data,
  });
}

/**
 * @description 商品管理 -- 生成属性
 * @param {Object} param data {Object} 传值参数
 */
export function generateAttrApi(data, id, type) {
  return request({
    url: `classes/generate_attr/${id}/${type}`,
    method: 'POST',
    data,
  });
}

/**
 * @description 商品属性 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function ruleListApi(params) {
  return request({
    url: `classes/classes/rule`,
    method: 'GET',
    params,
  });
}

/**
 * @description 商品属性 -- 添加
 * @param {Number} param id {Number} 属性id
 * @param {Object} param data {Object} 传值参数
 */
export function ruleAddApi(data, id) {
  return request({
    url: `classes/classes/rule/${id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description 商品属性 -- 详情
 * @param {Number} param id {Number} 属性id
 */
export function ruleInfoApi(id) {
  return request({
    url: `classes/classes/rule/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品评价 -- 虚拟评价
 * @id--产品id；
 */
export function fictitiousReply(id) {
  return request({
    url: `classes/reply/fictitious_reply/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品属性 -- 获取规则属性模板
 */
export function classesGetRuleApi() {
  return request({
    url: `classes/classes/get_rule`,
    method: 'get',
  });
}

/**
 * @description 商品 -- 获取运费模板
 */
export function classesGetTemplateApi(data) {
  return request({
    url: `classes/classes/get_template`,
    method: 'get',
    params: data,
  });
}

/**
 * @description 获取上传参数
 */
export function classesGetTempKeysApi(data) {
  return request({
    url: `classes/classes/get_temp_keys`,
    method: 'get',
    params: data,
  });
}

/**
 * @description 商铺产品 -- 导出
 */
export function storeClassesApi(data) {
  return request({
    url: `export/storeclasses`,
    method: 'get',
    params: data,
  });
}

/**
 * @description 添加商品 -- 检测活动存在
 */
export function checkActivityApi(id) {
  return request({
    url: `classes/classes/check_activity/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品添加编辑-- 用户标签
 */
export function labelListApi() {
  return request({
    url: 'user/user_label',
    method: 'get',
  });
}

/**
 * @description 商品分类-- cascader
 */
export function cascaderListApi(type) {
  return request({
    url: `classes/category/cascader_list/${type}`,
    method: 'get',
  });
}

/**
 * @description 商品管理-- 临时保存
 */
export function classesCache() {
  return request({
    url: 'classes/cache',
    method: 'get',
  });
}

/**
 * @description 商品管理-- 取消临时保存
 */
export function cacheDelete() {
  return request({
    url: 'classes/cache',
    method: 'delete',
  });
}

/**
 * @description 商品管理-- 取消临时保存
 */
export function getclassesAttr(id) {
  return request({
    url: 'classes/classes/get_attr/' + id,
    method: 'get',
  });
}

/**
 * @description 商品管理-- 关联用户标签
 */
export function classesUserLabel() {
  return request({
    url: 'classes/user_label',
    method: 'get',
  });
}

/**
 * @description 添加商品-- 商品标签
 */
export function classesStoreLabel() {
  return request({
    url: 'classes/classes_label',
    method: 'get',
  });
}

/**
 * @description 添加商品-- 商品标签
 */
export function classesAllEnsure() {
  return request({
    url: 'classes/all_ensure',
    method: 'get',
  });
}

/**
 * @description 添加商品-- 添加商品标签
 */
export function classesLabelAdd() {
  return request({
    url: 'classes/label/form',
    method: 'get',
  });
}

/**
 * @description 添加商品-- 添加商品参数
 */
export function classesAllSpecs() {
  return request({
    url: 'classes/all_specs',
    method: 'get',
  });
}

/**
 * @description 商品管理-- 添加品牌-获取上级分类
 */
export function brandCascader() {
  return request({
    url: 'classes/brand/cascader_list',
    method: 'get',
  });
}

/**
 * @description 商品管理-- 提交添加品牌
 */
export function classesBrand(data) {
  return request({
    url: 'classes/brand',
    method: 'POST',
    data,
  });
}
/**
 * @description 商品管理-- 提交添加品牌
 */
export function classesBrandlist(data) {
  return request({
    url: 'classes/brand',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品管理-- 提交编辑品牌
 */
export function classesBrandrev(id, data) {
  return request({
    url: `classes/brand/${id}`,
    method: 'put',
    data,
  });
}

/**
 * @description 商品管理-- 修改品牌状态
 */
export function classesBrandShow(id, show) {
  return request({
    url: `classes/brand/set_show/${id}/${show}`,
    method: 'put',
  });
}

/**
 * @description 商品管理-- 添加商品品牌列表
 */
export function brandList() {
  return request({
    url: `classes/brand/cascader_list/2`,
    method: 'get',
  });
}

/**
 *商品列表-获取商品规格
 */
export function classesAttrsApi(id) {
  return request({
    url: `classes/classes/attrs/${id}`,
    method: 'get',
  });
}

/**
 *商品列表-提交商品规格库存
 */
export function classesSaveStocksApi(data, id) {
  return request({
    url: `classes/classes/saveStocks/${id}`,
    method: 'PUT',
    data,
  });
}

/**
 *添加商品-获取所有商品单位列表
 */
export function classesAllUnit(id) {
  return request({
    url: `classes/get_all_unit`,
    method: 'get',
  });
}

/**
 *添加商品-商品单位添加表单
 */
export function classesUnitCreate(id) {
  return request({
    url: `classes/unit/create`,
    method: 'get',
  });
}

/**
 * @description 商品添加编辑-- 获取上传视频类型
 */
export function uploadType() {
  return request({
    url: 'file/upload_type',
    method: 'get',
  });
}

/**
 * @description 商品添加编辑-- 商品单位列表
 */
export function classesUnit(data) {
  return request({
    url: 'classes/unit',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品添加编辑-- 编辑单位
 */
export function classesUnitEdit(id) {
  return request({
    url: `classes/unit/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description 商品添加编辑-- 倒入卡密
 */
export function importCard(data) {
  return request({
    url: 'classes/classes/import_card',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品添加编辑-- 下载卡密
 */
export function exportclassesCard(data) {
  return request({
    url: 'export/storeclassesCardTemplate',
    method: 'get',
  });
}

/**
 * @description 商品标签组-- 添加
 */
export function labelCateCreate() {
  return request({
    url: 'classes/label_cate/create',
    method: 'get',
  });
}

/**
 * @description 商品标签组-- 列表
 */
export function classesLabelCate(data) {
  return request({
    url: 'classes/label_cate',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品标签组-- 编辑
 */
export function classesLabelEdit(id) {
  return request({
    url: `classes/label_cate/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description 商品标签-- 保存标签
 */
export function classesLabel(data) {
  return request({
    url: `classes/label/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 商品服务保障- 添加
 */
export function classesEnsureCreate() {
  return request({
    url: `classes/ensure/create`,
    method: 'get',
  });
}

/**
 * @description 商品保障服务-- 列表
 */
export function classesEnsure(data) {
  return request({
    url: 'classes/ensure',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品保障服务-- 编辑
 */
export function classesEnsureEdit(id) {
  return request({
    url: `classes/ensure/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description 商品保障服务--是否显示
 */
export function ensureSetShow(data) {
  return request({
    url: `classes/ensure/set_show/${data.id}/${data.is_show}`,
    method: 'put',
  });
}

/**
 * @description 商品参数-- 编辑
 */
export function classesSpecs(data) {
  return request({
    url: `classes/specs/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 商品参数-- 列表
 */
export function classesSpecsList(data) {
  return request({
    url: 'classes/specs',
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品参数-- 获取单个商品参数模版信息
 */
export function classesSpecsInfo(id) {
  return request({
    url: `classes/specs/${id}`,
    method: 'get',
  });
}

/**
 * @description 商品评价-- 回复评价列表
 */
export function classesReplycomment(data, id) {
  return request({
    url: `classes/reply/comment/${id}`,
    method: 'get',
    params: data,
  });
}

/**
 * @description 商品评价-- 保存管理员回复
 */
export function classesReplySave(data, replyId, id) {
  return request({
    url: `classes/reply/save_comment/${replyId}/${id}`,
    method: 'post',
    data: data,
  });
}

/**
 * @description 保存云端视频附件记录
 * @param {String} param ids {String}
 */
export function videoAttachment(data) {
  return request({
    url: 'file/video_attachment',
    method: 'post',
    data,
  });
}

/**
 * 添加虚拟评论
 * @param {*} data
 * @returns
 */
export function saveFictitiousReply(data) {
  return request({
    url: 'classes/reply/save_fictitious_reply',
    method: 'post',
    data,
  });
}

/**
 * 商品批量操作
 * @param {*} data
 * @returns
 */
export function batchProcess(data) {
  return request({
    url: 'classes/batch_process',
    method: 'post',
    data,
  });
}

/**
 * diy系统表单列表（全部）
 * @param {*} type
 * @returns
 */
export function allSystemForm() {
  return request({
    url: `/system/form/all_system_form`,
    method: 'get',
  });
}
