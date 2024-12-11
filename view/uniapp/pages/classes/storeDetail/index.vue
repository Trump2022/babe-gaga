<template>
	<view :style="colorStyle">
		<image :src="info.image" class="swiper"></image>
		<view class="card frist">
			<view class="card-header">
				<view class="title">
					{{info.name}}
				</view>
				<view class="location-box" @click="getLocation('openMap')">
					<view class="location">
						<view class='iconfont icon-ditu font-num'></view>
						<view class="address">
							{{info.address}}
						</view>
					</view>
					<view class="right">
						<text>距{{info.distance | convertToKm}}</text>
						<view class="iconfont icon-xiangyou"></view>
					</view>
				</view>
			</view>
		</view>
		<view class="card">
			<view class="card-header hospital-header">
				<view class="title card-title">
					门店介绍
				</view>
			</view>
			<view class="card-body">
				<parser :html="info.introduction" ref="article" :tag-style="tagStyle"></parser>
			</view>
		</view>
		<overbookingCom @click="goto" btnText="预约服务"></overbookingCom>
	</view>
</template>

<script>
	import { storeInfo } from '@/api/store'
	// import overbookingCom from '../components/overbookingCom'
	import parser from "@/components/jyf-parser/jyf-parser";
	import colors from '@/mixins/color.js';
	export default {
		mixins:[colors],
		components: {
			// overbookingCom,
			parser
		},
		data() {
			return {
				id: 0,
				info: {},
				active: 0,
				departActive: 0,
				scrollLeft: 0
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
		onLoad(opt) {
			this.id = opt.id
		},
		onShow() {
			this.getLocation()
		},
		methods: {
			getDetail(data) {
				storeInfo(this.id, data).then(res => {
					this.info = res.data
				})
			},
			goto() {
				uni.navigateTo({
					url: `/pages/accompanying_services/index/index?hospital_id=${this.id}`
				})
			},
			getLocation(open) {
				if(open) {
					// uni.openLocation({
					// 	latitude: this.info.latitude * 1,
					// 	longitude: this.info.longitude * 1,
					// 	address: this.info.address
					// });
					return uni.navigateTo({
						url: `/pages/classes/map/index?startAddress=我的位置&endAddress=${this.info.name}&startLatitude=${this.location.latitude}&startLongitude=${this.location.longitude}&endLatitude=${this.info.latitude}&endLongitude=${this.info.longitude}`
					})
				}
				uni.getLocation({
					type: 'wgs84',
					success: (res) => {
						let data = {
							longitude: res.longitude,
							latitude: res.latitude
						}
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
						this.location = data
						if(open) {
							// uni.openLocation({
							// 	latitude: this.info.latitude * 1,
							// 	longitude: this.info.longitude * 1,
							// 	address: this.info.address
							// });
							return uni.navigateTo({
								url: `/pages/classes/map/index?startAddress=我的位置&endAddress=${this.info.name}&startLatitude=${this.location.latitude}&startLongitude=${this.location.longitude}&endLatitude=${this.info.latitude}&endLongitude=${this.info.longitude}`
							})
						}
						this.getDetail(data)
					},
					fail: e => {
						const data = { latitude: uni.getStorageSync('user_latitude'), longitude: uni.getStorageSync('user_longitude') }
						wx.getSetting({
						  success: (res) => {
						    if (res.authSetting['scope.userLocation'] === false) {
						      // 用户未授权定位权限，可以提示用户，并打开授权设置页面
						      wx.showModal({
						        title: '提示',
						        content: '需要获取您的定位权限',
						        success: (modalRes) => {
						          if (modalRes.confirm) {
						            wx.openSetting({
						              success: (settingRes) => {
						                // 用户在设置页面授权后的回调
						                if (settingRes.authSetting['scope.userLocation'] === true) {
						                  // 用户重新授权了定位权限，可以进行相应操作
										  this.getDetail(data)
						                }
						              },
									  complete: e=> {
										  this.getDetail(data)
									  }
						            });
						          } else {
									  this.getDetail(data)
								  }
						        }
						      });
						    } else {
						      // 用户已经授权定位权限，可以进行相应操作
							  this.getDetail(data)
						    }
						  }
						});
					}
				})
			},
			goDoctor(index) {
				this.scrollLeft = 0
				this.active = 2
				// this.departActive = index
				this.$nextTick(() => {
					const query = uni.createSelectorQuery().in(this);
					query.select('.depart' + index).boundingClientRect(data => {
					  this.scrollLeft = data.left - 24
					}).exec();
					this.departActive = index
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	.swiper {
		height: 500rpx;
		width: 100%;
	}
	
	.card {
		padding: 24rpx;
		background-color: #fff;
		margin: 24rpx;
		border-radius: 12rpx;
		
		.card-header {
			
			.location-box {
				display: flex;
				align-items: center;
				justify-content: space-between;
				font-size: 26rpx;
				margin-top: 30rpx;
				
				.location {
					color: #999;
					display: flex;
					align-items: flex-start;
					flex: 1;
					
					.address {
						margin-left: 10rpx;
					}
				}
				
				.right {
					display: flex;
					align-items: center;
				}
			}
			
			.tag {
				background-color: #FFEFEC;
				border-radius: 18rpx;
				color: #C4004A;
				font-size: 22rpx;
				margin: 12rpx 0 17rpx;
				display: inline-block;
				width: 124rpx;
				height: 36rpx;
				display: flex;
				align-items: center;
				justify-content: center;
			}
		}
		
		.hospital-header {
			display: flex;
		}
		
		.card-title {
			flex: 1;
			display: flex;
			position: relative;
		}
		
		.active {
			color: var(--view-theme);
			
			&::after {
				display: block;
				content: '';
				position: absolute;
				bottom: -24rpx;
				left: 50%;
				width: 100rpx;
				margin-left: -50rpx;
				height: 8rpx;
				border-radius: 40rpx;
				background-color: var(--view-theme);
			}
		}
		
		.card-body {
			padding-top: 24rpx;
			border-top: 1rpx solid #EBEBEB;
			margin-top: 24rpx;
		}
		
		.depart {
			// display: flex;
			// flex-wrap: wrap;
			border: 1rpx solid #DDDDDD;
			border-radius: 12rpx;
			overflow: hidden;
			box-sizing: border-box;
			
			.depart-item {
				height: 74rpx;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				border: 1rpx solid #DDDDDD;
				margin: -1rpx;
				// flex-basis: 33.33%;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				min-width: 50%;
				box-sizing: border-box;
				border-right: none;
				border-top: none;
			}
		}
		
		.depart-doctor {
			white-space: nowrap;
			width: 100%;
			
			.doctor-depart-item {
				// white-space: nowrap;
				margin-right: 70rpx;
				display: inline-block;
				padding-bottom: 20rpx;
				
				&:last-child {
					margin-right: 0;
				}
			}
			
			.depart-active {
				color: var(--view-theme);
				position: relative;
				
				&::after {
					content: '';
					position: absolute;
					display: block;
					bottom: 0;
					left: 0;
					width: 100%;
					height: 4rpx;
					border-radius: 40rpx;
					background-color: var(--view-theme);
				}
			}
		}
			
		.doctor-list {
			min-height: 560rpx;
			.doctor-item {
				display: flex;
				padding: 24rpx 0;
				border-bottom: 1rpx solid #ddd;
				
				&:last-child {
					border-bottom: none;
				}
				
				image {
					width: 140rpx;
					height: 140rpx;
					border-radius: 12rpx;
					margin-right: 18rpx;
				}
				
				.doctor-info {
					flex: 1;
					font-size: 22rpx;
					color: #999;
					.doctor-name {
						display: flex;
						align-items: center;
						font-size: 30rpx;
						font-weight: bold;
						color: #222;
						margin-bottom: 22rpx;
						
						text {
							font-weight: normal;
							font-size: 24rpx;
							color: #666;
							margin-left: 24rpx;
						}
					}
				}
			}
		}
	}
	
	.title {
		font-weight: bold;
		font-size: 36rpx;
		color: #222;
	}
	
	.info {
		font-size: 26rpx;
		color: #999;
		margin-top: 10rpx;
	}
	
	.server-pirce, .server-specifications {
		font-size: 24rpx;
		color: #999;
		display: flex;
		align-items: center;
	}
	
	.server-pirce {
		margin-bottom: 18rpx;
	}
	
	.pirce {
		font-weight: bold;
		color: #E81A1A;
		font-size: 36rpx;
	}
	
	.specifications {
		color: #666;
	}
	
	.frist {
		margin-top: -100rpx;
		position: relative;
		z-index: 2;
	}
	.card-title {
		font-size: 30rpx;
	}
	.flowchart-image {
		display: block;
		width: 100%;
	}
</style>