<template>
  <!-- 客服列表 -->
  <view v-if="show" :style="colorStyle">
    <view class="discountInfo on">
      <view class="title">客服列表<text class="iconfont icon-guanbi5" @click="closeDiscount"></text></view>
      <view class="list">
        <view class="item" v-for="(item,index) in customerList" :key="index">
          <image :src="item.avatar" mode="" class="img"></image>
          <view class="text">{{item.staff_name}}</view>
          <view class="contact" @click="callPhone(item)">
            联系客服
          </view>
        </view>
      </view>
      <slot name="bottom"></slot>
    </view>
    <view class="mask" @touchmove.prevent :hidden="false" @click="closeDiscount"></view>
  </view>
</template>

<script>
   import colors from "@/mixins/color";
  export default {
    props: {
      customerList: {
        type: Array,
        default: []
      },
			customerType:{
				type:Number,
				default:1
			}
    },
    mixins:[colors],
    data() {
      return {
        show: false,
      };
    },
    mounted() {},
    methods: {
      closeDiscount() {
        this.$emit('closeKefu')
      },
      callPhone(item) {
				if(this.customerType == 1){
					uni.makePhoneCall({
						phoneNumber: item.customer_phone //仅为示例
					});
				}else{
					uni.navigateTo({
					  url: '/pages/store/service/index?id='+item.id
					})
				}
      }
    }
  }
</script>

<style scoped lang="scss">
  .discountInfo {
    position: fixed;
    bottom: 0;
    width: 100%;
    left: 0;
    background-color: #fff;
    z-index: 300;
    border-radius: 16rpx 16rpx 0 0;
    transform: translate3d(0, 100%, 0);
    transition: all .3s cubic-bezier(.25, .5, .5, .9);
    padding-bottom: 22rpx;
    padding-bottom: calc(22rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
    padding-bottom: calc(22rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/

    .title {
      font-size: 32rpx;
      color: #282828;
      text-align: center;
      margin: 38rpx 0 36rpx 0;
      position: relative;

      .iconfont {
        position: absolute;
        right: 30rpx;
        top: 0;
        font-size: 36rpx;
      }
    }

    .list {

      height: 750rpx;
      margin: 30rpx 30rpx 0 30rpx;
      overflow-x: hidden;
      overflow-y: auto;

      .item {
        height: 120rpx;
        width: 100%;
        border-bottom: 1px solid #eee;
        background-color: #fff;
        padding: 30rpx 30rpx;
        position: relative;
        display: flex;
        align-items: center;

        .img {
          margin-left: 6rpx;
          width: 80rpx;
          height: 80rpx;
          border-radius: 50%;
          border: 1px solid #EEEEEE;
        }

        .text {
          margin-left: 20rpx;
          font-size: 28rpx;
          font-weight: 400;
          color: #333333;

        }

        .contact {
          position: absolute;
          right: 30rpx;
          width: 140rpx;
          height: 48rpx;
          text-align: center;
          line-height: 48rpx;
          background-color: var(--view-minorColorT);
          font-size: 24rpx;
          font-weight: 400;
          border-radius: 24rpx;
          color: var(--view-theme);
        }
      }
    }
  }

  .on {
    transform: translate3d(0, 0, 0);
  }
</style>
