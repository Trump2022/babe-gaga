<template>
  <view>
    <!-- 顶部搜索 -->
    <view class="search">
      <input type="text" placeholder="搜索门店" class="search-box" />
    </view>
    <!-- 门店列表 -->
    <view class="content" v-if="!defaultShow">
      <view class="list">
        <view class="left">
          <view class="name">
            小米之家陕西西安碑林区店
          </view>
          <view class="adress">
            陕西省咸阳市奥林匹克花园三期北京 奥运村
          </view>
          <view class="adress">
            营业时间：10:00-21:00
          </view>
        </view>
        <view class="right">
          <view class="gostore">
            进店选购
          </view>
          <view class="distance">
            距离2.1km
          </view>
          <view class="telephone">
            <view class="phone">
              <text class="iconfont icon-dianhau"></text>
            </view>
            <view class="phone">
              <text class="iconfont icon-weizhi"></text>
            </view>


          </view>
        </view>
      </view>
    </view>
 <!-- 缺省页 -->
 <view class="default">
<image :src="imgHost+'/statics/images/store-default.png'" alt="" class="img" />
<view class="text">
  暂无门店信息，再去试试其他地址吧~
</view>
 </view>
  </view>
</template>

<script>
	import {
	  HTTP_REQUEST_URL
	} from '@/config/app';
  export default {
    data() {
      return {
        defaultShow:true,
				imgHost: HTTP_REQUEST_URL,
      };
    }
  }
</script>

<style lang="scss">
  .search {
    width: 100%;
    height: 92rpx;
    background-color: #fff;
    padding: 0 30rpx;

    .search-box {
      position: relative;
      padding-left: 20rpx;
      width: 100%;
      height: 60rpx;
      background: #F5F5F5;
      border-radius: 30rpx;

      font-size: 26rpx;
      font-weight: 400;
      color: #CCCCCC;

      .icon-sousuo6 {
        margin-left: 24rpx;
        margin-right: 10rpx;
        font-size: 30rpx;
      }
    }
  }

  .content {

    width: 100%;
    height: 100%;
    padding: 0 30rpx;

    .list {
      display: flex;
      margin-top: 20rpx;
      width: 100%;
      height: 218rpx;
      background: #FFFFFF;
      border-radius: 12rpx;

      .left {
        flex: 2;
        padding: 28rpx 0 0 30rpx;

        .name {
          font-size: 28rpx;
          font-weight: 500;
          color: #333333;
        }

        .adress {
          width: 352rpx;
          margin-top: 18rpx;
          word-wrap: break-word;
          font-size: 22rpx;
          font-weight: 400;
          color: #888888;
        }
      }

      .right {
        margin: 52rpx 0 0 100rpx;
        flex: 1;

        .gostore {
          font-size: 22rpx;
          font-weight: 400;
          color: #E93323;
        }

        .distance {
          margin-top: 14rpx;
          font-size: 20rpx;
          font-weight: 400;
          color: #999999;
        }

        .telephone {
          margin-top: 14rpx;
          display: flex;

          .phone {
            margin-right: 24rpx;
            width: 40rpx;
            height: 40rpx;
            background-color: pink;
            border-radius: 50%;
            text-align: center;
            line-height: 40rpx;

            .icon-dianhau {

              font-size: 25rpx;
              color: red !important;

            }

            .icon-weizhi {
              font-size: 25rpx;
              color: red !important;
            }
          }

        }
      }
    }
  }
  .default {
    width: 100%;
    height: 100%;
    .img {
      width: 414rpx;
      height: 256rpx;
    }
  }
  .default {
    margin-top: 80rpx;
display: flex;
flex-direction: column;
align-items: center;
.text {
  margin-top: 54rpx;
  font-size: 26rpx;
  font-weight: 400;
  color: #999999;
}
  }
</style>
