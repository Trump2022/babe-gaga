<template>
	<view class="box">
		<view class="title">
			<text>全国门店</text>
			<view class="more" @click="goto">
				<text>更多</text>
				<image src="/static/images/check_more.png" mode="widthFix"></image>
			</view>
		</view>
		
		<scroll-view  scroll-x>
			<view class="list">
				<view class="item" v-for="(item, index) in list" :key="item.id" @click="gotoDetail(item.id)">
					<view class="image-box">
						<view class="recently" v-if="index == 0">
							离你最近
						</view>
						<image :src="item.image" mode=""></image>
					</view>
					<view class="text-box name-box">
						<text class="name">{{item.name}}</text>
						<text class="iconfont icon-xiangyou"></text>
					</view>
					<view class="text-box name-box">
						<text class="address">{{item.detailed_address}}</text>
						<text>{{item.distance | convertToKm}}</text>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import {
		storeList
	} from '@/api/store.js';
	export default {
		data() {
			return {
				location: {
					latitude: 0,
					longitude: 0
				},
				list: []
			}
		},
		filters: {
			convertToKm(val) {
				if(!val) return '0km'
				if(val > 1000) {
					return val / 1000 + 'km'
				} else {
					return val + 'm'
				}
			}
		},
		mounted() {
			this.selfLocation()
		},
		methods: {
			goto() {
				uni.navigateTo({
					url: '/pages/classes/storeList/index'
				})
			},
			gotoDetail(id) {
				uni.navigateTo({
					url: '/pages/classes/storeDetail/index?id=' + id
				})
			},
			getStoreList() {
				storeList(this.location).then(res => {
					console.log(res)
					this.list = res.data
				})
			},
			selfLocation() {
				let self = this
				// #ifdef MP || APP-PLUS
				uni.getLocation({
					type: 'gcj02',
					success: (res) => {
						try {
							uni.setStorageSync('user_latitude', res.latitude);
							uni.setStorageSync('user_longitude', res.longitude);
							self.location = { latitude: res.latitude, longitude: res.longitude };
							self.getStoreList();
						} catch {}
					},
					fail:(res)=>{
						// #ifdef MP
						uni.getSetting({
							success: res=>{
								if(typeof(res.authSetting['scope.userLocation']) != 'undefined' && !res.authSetting['scope.userLocation']){
								  uni.setStorageSync('refuseLocation', true);
								}
								self.location = { latitude: uni.getStorageSync('user_latitude'), longitude: uni.getStorageSync('user_longitude') };
								self.getStoreList(self.location.latitude,self.location.longitude);
							}
						})
						// #endif
					}
				});
				// #endif
				// #ifdef H5
				if (this.$wechat.isWeixin()) {
					this.$wechat.location().then(res => {
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
						self.getStoreList();
					})
				} else {
					uni.getLocation({
						type: 'gcj02',
						success: function(res) {
							try {
								uni.setStorageSync('user_latitude', res.latitude);
								uni.setStorageSync('user_longitude', res.longitude);
								self.getStoreList();
							} catch {}
						}
					});
				}
				// #endif
			},
		}
	}
</script>

<style scoped lang="scss">
	.box {
		background-color: #fff;
		border-radius: 18rpx;
		margin: 48rpx 18rpx;
		padding-bottom: 30rpx;
		
		.title {
			padding: 30rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			font-size: 42rpx;
			color: #231815;
			font-weight: bold;
		}
		
		.more {
			display: flex;
			align-items: center;
			font-size: 22rpx;
			font-weight: normal;
			
			image {
				width: 48rpx;
				margin-left: 10rpx;
			}
		}
		
		.list {
			display: flex;
			padding: 0 30rpx;
			overflow-x: auto;
			padding-right: 0;
			
			.item {
				margin-right: 30rpx;
				width: 500rpx;
				
				.image-box {
					position: relative;
					
					.recently {
						position: absolute;
						left: 0;
						bottom: 0;
						z-index: 3;
						width: 106rpx;
						height: 42rpx;
						background-color: #231815;
						display: flex;
						align-items: center;
						justify-content: center;
						color: #fff;
						font-size: 20rpx;
						border-radius: 12rpx;
						border-top-left-radius: 0;
						border-bottom-right-radius: 0;
					}
				}
				
				image {
					width: 500rpx;
					height: 280rpx;
					border-radius: 12rpx;
					display: block;
				}
				
				.text-box {
					display: flex;
					justify-content: space-between;
					align-items: center;
					
					.name {
						color: #231815;
						font-weight: bold;
						font-size: 32rpx;
						text-overflow: ellipsis;
						overflow: hidden;
						white-space: nowrap;
						display: block;
					}
					
					text {
						font-size: 24rpx;
						color: #97A1AB;
					}
					
					.address {
						width: 80%;
						white-space: nowrap;
						overflow: hidden;
						text-overflow: ellipsis;

					}
				}
				
				.name-box {
					margin: 18rpx 0 6rpx;
				}
			}
		}
	}
</style>