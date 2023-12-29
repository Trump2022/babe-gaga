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
 * 获取短视频列表
 * @param {com} data 列表
 */
export function videoList (data) {
    return request({
        url: `/marketing/video/index`,
        method: 'get',
        params: data
    });
}

/**
 * 获取短视频信息
 * @param {com} data 详情
 */
export function videoInfo (id) {
    return request({
        url: `/marketing/video/info/${id}`,
        method: 'get'
    });
}

/**
 * 保存短视频
 * @param {com} data 提交
 */
export function videoSave (data,id) {
    return request({
        url: `/marketing/video/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 短视频上下架
 * @param {com} data 上下架
 */
export function videoSetStatus (data) {
    return request({
        url: `/marketing/video/set_status/${data.id}/${data.status}`,
        method: 'get'
    });
}

/**
 * 短视频推荐
 * @param {com} data 推荐
 */
export function videoSetRecommend (data) {
    return request({
        url: `/marketing/video/set_recommend/${data.id}/${data.recommend}`,
        method: 'get'
    });
}

/**
 * 短视频审核
 * @param {com} data 审核
 */
export function videoVerify (data) {
    return request({
        url: `/marketing/video/verify/${data.id}/${data.verify}`,
        method: 'get'
    });
}

/**
 * 短视频强制下架
 * @param {com} data 强制
 */
export function videoTakeDown (id) {
    return request({
        url: `/marketing/video/take_down/${id}`,
        method: 'get'
    });
}

/**
 * 获取短视频评论列表
 * @param {com} data 列表
 */
export function videoComment (data) {
    return request({
        url: `/marketing/video/comment`,
        method: 'get',
        params: data
    });
}

/**
 * 评论回复提交
 * @param {com} data 提交
 */
export function videoReply (data,id) {
    return request({
        url: `/marketing/video/comment/reply/${id}`,
        method: 'post',
        data
    });
}

/**
 * 获取评论回复列表
 * @param {com} data 回复列表
 */
export function videoCommentReply (data,id) {
    return request({
        url: `/marketing/video/comment/reply/${id}`,
        method: 'get',
        params:data
    });
}

/**
 * 获取虚拟评论表单
 * @param {com} data 回复列表
 */
export function fictitiousReply (video_id) {
    return request({
        url: `/marketing/video/comment/fictitious/${video_id}`,
        method: 'get'
    });
}