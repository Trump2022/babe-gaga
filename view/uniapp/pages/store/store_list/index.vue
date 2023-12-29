<template>
	<view :style="colorStyle">
		<view class="navbar">
			<view :class="{ on: content.store_type === 1 }" class="item" @tap="navChange(1)">选择门店</view>
			<view :class="{ on: content.store_type === 2 }" class="item" @tap="navChange(2)">常用门店</view>
		</view>
		<view class="header_search" v-if="!header">
			<!-- <view class="title">
				搜索门店
			</view> -->
			<view class="box acea-row row-center-wrapper">
				<input type="text" placeholder="搜索门店" class="input" focus @input="setValue" v-model="content.keyword"></input>
				<text class="iconfont icon-sousuo6" />
			</view>
		</view>
		<!-- <transition-group name="lyric" v-if="header">
			<map style="width: 100%; height: 668rpx;" :latitude="latitudeStore" :longitude="longitudeStore" :markers="covers" v-if="map" :key="1" />
		</transition-group> -->

		<!-- 附近门店 -->
		<view class="nearby" v-if="header">
			<view class="store acea-row row-middle">
				<!-- <view class="btn" :class="{'activeColor':active==1}" @click="btnFn(1)">
					附近门店
				</view> -->
				<view class="area">
					<view class="btn" :class="{'activeColor':active==2}" @click="btnFn(2)">
						{{ addressInfo.length ? addressInfo[1].label : '全部门店' }}
						<text class="iconfont icon-xiangxia"></text>
					</view>
				</view>
				<view class="search" @click="searchMap()">
					<text class="iconfont icon-sousuo6"></text>
				</view>
				<view class="search" @click="goMap">
					<text class="iconfont icon-ditu1"></text>
				</view>
				<!-- <view class="put" @click="putMap()">
					<text class="iconfont icon-xiangshang" v-if="map==1" />
					<text class="iconfont icon-xiangxia" v-else />
					{{map==1?"收起地图":"展开地图"}}
				</view> -->
			</view>
		</view>
		<!-- 门店列表 -->
		<view class="content" v-if="info.length>0">
			<view class="list acea-row row-between-wrapper" v-for="(item,index) in info" :key="index" @click="activeFn(item)" :class="{active:sortIndex==item.id}">
				<text class="iconfont icon-xuanzhong6" v-if="sortIndex==item.id"></text>
				<view class="left">
					<view class="name line2">
						{{item.name}}
					</view>
					<view class="adress acea-row">
						<text class="iconfont icon-dingwei2" />
						<view class="con">{{item.detailed_address}}</view>
					</view>
					<view class="adress">
						<text class="iconfont icon-yingyeshijian2" />
						营业时间：{{item.day_time}}
					</view>
				</view>
				<view class="right">
					<view class="gostore">
						进店选购
					</view>
					<view class="distance">
						距离{{item.range}}km
					</view>
					<view class="telephone">
						<view class="phone" @click.stop="callPhone(item)">
							<text class="iconfont icon-dianhua"></text>
						</view>
						<view class="phone" @click.stop="showMaoLocation(item)">
							<text class="iconfont icon-dingwei2"></text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<!-- 缺省页 -->
		<view class="default" v-if="info.length==0">
			<image :src="imgHost+'/statics/images/store-default.png'" mode="" class="img"></image>
			<view class="text">
				暂无门店信息，再去试试其他地址吧~
			</view>
		</view>
		<areaWindow ref="areaWindow" :display="display" :cityShow="2" :address="addressInfo" @submit="OnChangeAddress" @changeClose="changeClose"></areaWindow>
	</view>
</template>

<script>
	import {
		HTTP_REQUEST_URL
	} from '@/config/app';
	import colors from "@/mixins/color";
	import {
		getList
	} from '@/api/new_store.js'
	import {
		getGeocoder,
		getCityList
	} from '@/api/user.js';
	import areaWindow from '@/components/areaWindow';
	export default {
		components: {
			areaWindow
		},
		mixins: [colors],
		data() {
			return {
				imgHost: HTTP_REQUEST_URL,
				sortIndex: 0,
				active: 1,
				content: {
					// 自己的位置
					latitude: uni.getStorageSync('user_latitude'),
					longitude: uni.getStorageSync('user_longitude'),
					store_type: 1,
					keyword: "",
					// limit: 10,
					page: 1,
					province: 0,
					city: 0,
					area: 0,
				},
				info: [],
				map: 1,
				header: 1,
				comeType: 0,
				// 门店位置
				latitudeStore: uni.getStorageSync('user_latitude'),
				longitudeStore: uni.getStorageSync('user_longitude'),
				covers: [],
				display: false,
				addressInfo: [],
			};
		},
		onLoad(options) {
			this.comeType = options.type
			this.sortIndex = options.storeId
			this.isCollage = options.isCollage
			this.getlist()
			// try {
			// 	if (this.content.latitude && this.content.longitude && options.storeFrom == 1) {
			// 		// this.getlist()
			// 		this.selfLocation();
			// 	} else {
			// this.selfLocation();
			// 	}
			// } catch (e) {
			// 	this.getlist()
			// }
		},
		methods: {
			goMap() {
				uni.navigateTo({
					// #ifdef APP
					url: `/pages/store/mapnvue/index?province=${this.content.province}&city=${this.content.city}&area=${this.content.area}`
					// #endif
					// #ifndef APP
					url: `/pages/store/map/index?province=${this.content.province}&city=${this.content.city}&area=${this.content.area}`
					// #endif
				})
			},
			navChange(type) {
				this.content.store_type = type;
				this.getlist();
			},
			getCityList({
				province,
				city,
				district
			}) {
				getCityList(`${province}/${city}/${district}`).then(({
					data
				}) => {
					this.addressInfo = data;
					this.content.province = data[0].value;
					this.content.city = data[1].value;
					this.content.area = data[2].value;
					this.getlist();
				});
			},
			getGeocoder(location) {
				getGeocoder(location).then(res => {
					const {
						province,
						city,
						district
					} = res.data.address_component;
					this.getCityList({
						province,
						city,
						district
					});
				});
			},
			selfLocation() {
				let self = this
				// #ifdef H5
				if (self.$wechat.isWeixin()) {
					self.$wechat.location().then(res => {
						this.content.latitude = res.latitude;
						this.content.longitude = res.longitude;
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
						self.getlist();
					}).catch(err => {
						self.getlist();
					})
				} else {
					// #endif	
					uni.getLocation({
						type: 'wgs84',
						success: ({
							latitude,
							longitude
						}) => {
							try {
								// this.content.latitude = res.latitude;
								// this.content.longitude = res.longitude;
								// uni.setStorageSync('user_latitude', res.latitude);
								// uni.setStorageSync('user_longitude', res.longitude);
								this.getGeocoder({
									lat: latitude,
									long: longitude
								});
							} catch {}
							// self.getlist();
						},
						complete: function() {
							// self.getlist();
						}
					});
					// #ifdef H5	
				}
				// #endif
			},
			// 门店列表
			getlist() {
				getList(this.content).then(res => {
					this.info = res.data
					res.data.forEach(item => {
						if (this.sortIndex == item.id) {
							this.latitudeStore = item.latitude;
							this.longitudeStore = item.longitude;
							this.covers.push({
								title: item.name,
								latitude: item.latitude,
								longitude: item.longitude,
								iconPath: item.image,
								width: 30,
								height: 30
							})
						}
					})
				})
			},
			// 输入关键字搜索门店
			setValue(e) {
				this.getlist()

			},
			// 点击高亮
			activeFn(row) {
				this.sortIndex = row.id
				if (this.comeType) {
					if (this.isCollage == 1) {
						uni.$emit('activeFn', row);
						uni.navigateBack();
						return;
					}
					uni.reLaunch({
						url: `/pages/store_cate/store_cate?mapFrom=1&id=` + row.id
					});
				}
			},
			// 搜索门店
			searchMap() {
				this.header = 0
			},
			// 收起地图
			putMap() {
				if (this.map == 1) {
					this.map = 0
				} else if (this.map == 0) {
					this.map = 1
				}

			},
			// 打电话
			callPhone(row) {
				uni.makePhoneCall({
					phoneNumber: row.phone //仅为示例
				});
			},
			btnFn(num) {
				// switch (num) {
				// 	case 1:
				// 		this.active = 1
				// 		this.content.store_type = 1
				// 		break;
				// 	case 2:
				// 		this.active = 2
				// 		this.content.store_type = 2
				// 		break;
				// }
				// this.getlist()
				this.display = true;
			},
			showMaoLocation(e) {
				let self = this;
				// #ifdef H5
				if (self.$wechat.isWeixin()) {
					return self.$wechat.seeLocation({
						latitude: Number(e.latitude),
						longitude: Number(e.longitude),
						name: e.name,
						scale: 13,
						address: `${e.address}-${e.detailed_address}`,
					}).then(res => {})
				}
				// #endif
				uni.openLocation({
					latitude: Number(e.latitude),
					longitude: Number(e.longitude),
					name: e.name,
					address: `${e.address}-${e.detailed_address}`,
					success: function() {
						Number
					}
				});
			},
			changeClose() {
				this.display = false;
			},
			OnChangeAddress(address) {
				this.addressInfo = address;
				this.content.province = address[0].value;
				this.content.city = address[1].value;
				this.content.area = address[2].value;
				this.getlist();
			}
		}
	}
</script>

<style lang="scss">
	.nearby .active,
	.content .active {
		position: relative;
		border: 1px solid var(--view-theme) !important;

		.icon-xuanzhong6 {
			font-size: 46rpx;
			position: absolute;
			bottom: -8rpx;
			right: -6rpx;
			color: var(--view-theme);
		}
	}

	.activeColor {
		background-color: var(--view-theme) !important;
		color: #fff !important;
	}

	.nearby {
		width: 100%;
		height: 132rpx;
		background-color: #fff;
		// border-radius: 40rpx 40rpx 0rpx 0rpx;
		// box-shadow: 0px -2px 22px 0px rgba(0, 0, 0, 0.0400);

		.store {
			height: 132rpx;
			padding: 0 30rpx;

			.btn {
				margin-right: 16rpx;
				width: 160rpx;
				height: 56rpx;
				// background: #F5F5F5;
				border-radius: 29rpx;
				text-align: center;
				line-height: 56rpx;
				font-size: 26rpx;
				font-weight: 400;
				color: #999999;
			}

			.search {
				display: flex;
				justify-content: center;
				align-items: center;
				width: 56rpx;
				height: 56rpx;
				border-radius: 50%;
				background-color: #F5F5F5;

				+.search {
					margin-left: 28rpx;
				}

				.iconfont {
					font-size: 30rpx;
					color: #666666;
				}
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
			background: #FFFFFF;
			border-radius: 12rpx;
			padding: 28rpx 30rpx;
			border: 1px solid #fff;
			overflow: hidden;

			.left {
				width: 450rpx;
				border-right: 1px solid #eee;
				padding-right: 20rpx;

				.name {
					font-size: 28rpx;
					font-weight: 500;
					color: #333333;
				}

				.adress {
					width: 400rpx;
					margin-top: 18rpx;
					word-wrap: break-word;
					font-size: 22rpx;
					font-weight: 400;
					color: #888888;

					.con {
						width: 360rpx;
					}

					.icon-dingwei2 {
						margin-right: 8rpx;
						font-size: 18rpx;
						color: #ccc;
						margin-top: 6rpx;
					}

					.icon-yingyeshijian2 {
						margin-right: 8rpx;
						font-size: 18rpx;
						color: #ccc;
					}
				}
			}

			.right {

				.gostore {
					font-size: 22rpx;
					font-weight: 400;
					color: var(--view-theme);
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
						position: relative;
						margin-right: 24rpx;
						width: 40rpx;
						height: 40rpx;
						background: var(--view-minorColorT);
						border-radius: 50%;
						text-align: center;
						line-height: 40rpx;
						z-index: 2;

						.icon-dingwei2 {
							font-size: 25rpx;
							color: var(--view-theme) !important;
						}

						.icon-dianhua {
							font-size: 25rpx;
							color: var(--view-theme) !important;
						}
					}

				}
			}
		}
	}

	.lyric-enter,
	.lyric-leave-to {
		opacity: 0;
		transform: translateY(60px);
	}

	.lyric-enter-to,
	.lyric-leave {
		opacity: 1;

	}

	.lyric-enter-active,
	.lyric-leave-active {
		transition: all 0.3s;
	}

	.header_search {
		.title {
			width: 100%;
			height: 86rpx;
			text-align: center;
			line-height: 86rpx;
			font-size: 30rpx;
			font-weight: 600;
			color: #282828;
			background-color: #fff;
		}

		.box {
			width: 100%;
			height: 92rpx;
			background: #fff;
			position: relative;

			.input {
				// margin-left: 30rpx;
				padding-right: 40rpx;
				padding-left: 80rpx;
				width: 690rpx;
				height: 60rpx;
				background: #F5F5F5;
				border-radius: 30rpx;
				font-size: 26rpx;
				font-weight: 400;
				box-sizing: border-box;
				// color: #CCCCCC;
			}

			.iconfont {
				content: "\e79b";
				position: absolute;
				left: 60rpx;
				top: 34rpx;
				font-size: 30rpx;
				color: #CCCCCC;
			}
		}

	}

	.default {
		display: flex;
		flex-direction: column;
		align-items: center;

		.img {
			width: 414rpx;
			height: 256rpx;
		}

		.text {
			margin-top: 54rpx;
			font-size: 26rpx;
			font-weight: 400;
			color: #999999;
		}
	}

	.navbar {
		display: flex;
		height: 90rpx;
		background-color: #FFFFFF;
		font-size: 30rpx;
		color: #999999;

		.item {
			flex: 1;
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			cursor: pointer;
		}

		.on {
			font-weight: 500;
			color: var(--view-theme);

			&::after {
				content: "";
				position: absolute;
				bottom: 0;
				left: 50%;
				width: 120rpx;
				height: 4rpx;
				background-color: var(--view-theme);
				transform: translateX(-50%);
			}
		}
	}

	.area {
		flex: 1;
		min-width: 0;
	}
</style>