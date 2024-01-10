<template>
	<!-- 底部导航 -->
	<view v-if="newData.status && newData.status.status">
		<view v-if="newData.bgColor">
			<view class="page-footer" id="target" :style="{'background-color':newData.bgColor.color[0].item}">
				<view class="foot-item" v-for="(item,index) in newData.menuList" :key="index" @click="goRouter(item)">
					<block v-if="item.link.split('?')[0] == activeRouter">
						<image :src="item.imgList[0]"></image>
						<view class="txt" :style="{color:newData.activeTxtColor.color[0].item}">{{item.name}}</view>
					</block>
					<block v-else>
						<image :src="item.imgList[1]"></image>
						<view class="txt" :style="{color:newData.txtColor.color[0].item}">{{item.name}}</view>
					</block>
					<!-- <div class="count-num"
						v-if="item.link === '/pages/order_addcart/order_addcart' && cartNum>0">
						{{cartNum}}
					</div> -->
					<uni-badge v-if="item.link === '/pages/order_addcart/order_addcart' && cartNum>0" class="uni-badge-left-margin" :text="cartNum" absolute="rightTop">
					</uni-badge>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters
	} from "vuex"
	import {
		getNavigation
	} from '@/api/public.js'
	import {
		getCartCounts,
	} from '@/api/order.js';
	export default {
		name: 'pageFooter',
		props: {},
		computed: mapGetters(['isLogin', 'cartNum']),
		created() {
			let routes = getCurrentPages(); //获取当前打开过的页面路由数组
			let curRoute = routes[routes.length - 1].route //获取当前页面路由
			this.activeRouter = '/' + curRoute
		},
		mounted() {
			this.navigationInfo();
			if (this.isLogin) {
				this.getCartNum()
			}
		},
		data() {
			return {
				newData: {},
				activeRouter:''
			}
		},
		methods: {
			navigationInfo(){
				getNavigation({type: 2}).then(res => {
					this.newData = res.data
					this.$emit('newDataStatus', this.newData.status?this.newData.status.status:false)
					if (this.newData.status && this.newData.status.status) {
						uni.hideTabBar()
					} else {
						uni.showTabBar()
					}
				})
			},
			goRouter(item) {
				var pages = getCurrentPages();
				var page = (pages[pages.length - 1]).$page.fullPath;
				if (item.link == page) return
				if(item.link == '/pages/short_video/appSwiper/index' || item.link == '/pages/short_video/nvueSwiper/index'){
					//#ifdef APP
					item.link = '/pages/short_video/appSwiper/index'
					//#endif
					//#ifndef APP
					item.link = '/pages/short_video/nvueSwiper/index'
					//#endif
				}
				uni.switchTab({
					url: item.link,
					fail(err) {
						uni.redirectTo({
							url: item.link
						})
					}
				})
			},
			getCartNum: function() {
				let id = uni.getStorageSync('user_store_id') || 0;
				getCartCounts(0,id).then(res => {
					this.$store.commit('indexData/setCartNum', res.data.count + '')
				});
			}
		}
	}
</script>

<style scoped lang="scss">
	.page-footer {
		position: fixed;
		bottom: 0;
		z-index: 666;
		display: flex;
		align-items: center;
		justify-content: space-around;
		width: 100%;
		height: calc(98rpx+ constant(safe-area-inset-bottom)); ///兼容 IOS<11.2/
		height: calc(98rpx + env(safe-area-inset-bottom)); ///兼容 IOS>11.2/
		box-sizing: border-box;
		border-top: solid 1rpx #F3F3F3;
		background-color: #fff;
		box-shadow: 0px 0px 17rpx 1rpx rgba(206, 206, 206, 0.32);
		padding-bottom: constant(safe-area-inset-bottom); ///兼容 IOS<11.2/
		padding-bottom: env(safe-area-inset-bottom); ///兼容 IOS>11.2/
		
		.foot-item {
			display: flex;
			width: max-content;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			position: relative;

			.count-num {
				position: absolute;
				display: flex;
				justify-content: center;
				align-items: center;
				width: 40rpx;
				height: 40rpx;
				top: 0rpx;
				right: -15rpx;
				color: #fff;
				font-size: 20rpx;
				background-color: #FD502F;
				border-radius: 50%;
				padding: 4rpx;
			}
		}

		.foot-item image {
			height: 50rpx;
			width: 50rpx;
			text-align: center;
			margin: 0 auto;
		}

		.foot-item .txt {
			font-size: 24rpx;


			&.active {}
		}
	}
</style>
