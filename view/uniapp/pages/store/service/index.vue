<template>
  <view class="body">
    <!-- 头像 -->
<view class="head-portrait">
  <img :src="info.avatar" alt="" class="img">
  <view class="name">
  {{info.staff_name}}
  </view>
</view>
<!-- 具体介绍 --> 
<view class="content">
  <view class="title">
   {{info.store_name}}
  </view>
  <view class="address">
   <text class="iconfont icon-dizhi1" />
   {{info.detailed_address}}
  </view>
  <view class="address">
   <text class="iconfont icon-shijian1"></text> 营业时间：{{info.day_start}}-{{info.day_end}}
  </view> 
</view>
<!-- 分割线 -->
<view class="line" />
  <!-- 企业微信图片 -->
  <view class="weixin-img">
    <!-- <view class="img"></view> -->
		<image :show-menu-by-longpress="true" :src="info.customer_url" alt="" class="img" @click="previewImage"></image>
    <view class="text">
      长按识别二维码
    </view>
    <view class="text">
      添加我的企业微信
    </view>
  </view>

  </view>
</template>

<script>
  import {getCustomerInfo} from '@/api/new_store.js'
  export default {
    data() {
      return {
        info:{},
        id:0,
      };
    },
    onShow() {
      this.getInfo()
    },
		onLoad(options){
			this.id = options.id
		},
    methods:{
      getInfo() {
        getCustomerInfo(this.id).then(res=>{
           this.info = res.data
        })
      },
			previewImage(){
				uni.previewImage({
							// 需要预览的图片链接列表
							urls: [],
							// 为当前显示图片的链接/索引值
							current: this.info.customer_url,
							// 图片指示器样式	
							indicator:'default',
							// 是否可循环预览
							loop:false,
							// 长按图片显示操作菜单，如不填默认为保存相册
							// longPressActions:{
							// 	itemList:[this.l('发送给朋友'),this.l]
							// },
							success: res => {}, 
							fail: err => {}
						});
			}
    }
  }
</script>

<style lang="scss">
page {
  background-color: #fff;
}
.head-portrait {
  margin-top: 40rpx;
  width: 100%;
  display: flex;
 flex-direction: column;
 align-items: center;
  .img {
    width: 140rpx;
    height: 140rpx;
    border-radius: 50%;

  }
  .name {
    font-size: 30rpx;
    font-weight: 400;
    color: #282828;
  }
}
.content {
  margin-top: 74rpx;
  padding-left: 60rpx;
  .title {
    margin-left: 10rpx;
    font-size: 34rpx;
    font-weight: 500;
    color: #333333;
    
  }
  .address {
    margin-top: 16rpx;
    font-size: 26rpx;
    font-weight: 400;
    color: #888888;
    .icon-dizhi1 {
      font-size: 26rpx;
      margin-right: 12rpx;
    }
    .icon-shijian1 {
      font-size: 26rpx;
      margin-right: 12rpx;
    }
  }
}
.body {
  padding: 0 30rpx;
}
.line {
margin-top: 56rpx;
  width: 100%;
  height: 1px;
  background-color: #eee;
margin-top: #eee;
}
.weixin-img {
  margin-top: 54rpx;
   width: 100%;
   display: flex;
  flex-direction: column;
  align-items: center;
  .img {
    width: 290rpx;
    height: 290rpx;

        margin-bottom: 38rpx;
  }
  .text {
    margin-top: 10rpx;
    font-size: 28rpx;
    font-weight: 500;
    color: #333333;
  }
}
</style>
