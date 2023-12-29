<template>
	<view class="" :style="colorStyle">
		<!-- #ifdef MP -->
		<view :style="{ height: `${statusBarHeight + navBarHeight}px` }"></view>
		<view class="nav-bar">
			<view :style="{ height: `${statusBarHeight}px` }"></view>
			<view :style="{ height: `${navBarHeight}px` }" class="content">
				<button class="button" hover-class="none" @tap="goBack">
					<text class="iconfont icon-fanhui2"></text>
				</button>
				拼单详情
			</view>
		</view>
		<!-- #endif -->
		<image class="header" :src="header"></image>
		<template v-if="status != -1">
			<view class="simple">
				<view class="seller">
					<view class="head acea-row row-between">
						<view class="acea-row headCon">
							<view class="tag">{{ delivery_type == 3 ? '配送' : '自提' }}</view>
							<view class="name">{{ storeInfo.name }}</view>
						</view>
						<view>距您{{ storeInfo.range || 0 }}km</view>
					</view>
					<view>{{ storeInfo.detailed_address }}</view>
				</view>
				<view class="buyer">
					<view v-if="plainPartake.length" class="avatar-group">
						<template v-for="(item, index) in plainPartake">
							<image v-if="index <= 6" :key="item.userInfo.uid" class="avatar" :src="item.userInfo.avatar || '/static/images/f.png'" mode="aspectFill"></image>
						</template>
						<view v-if="plainPartake.length > 7" class="gengduo"><text class="iconfont icon-gengduo"></text></view>
					</view>
					<view>{{ plainPartake.length }}人已点单</view>
					<!-- #ifdef MP -->
					<button v-if="status != 2" class="button" hover-class="none" @tap="refresh"><text class="iconfont icon-shuaxin"></text>刷新</button>
					<!-- #endif -->
					<!-- #ifdef H5 -->
					<button v-if="$wechat.isWeixin() && status != 2" class="button" hover-class="none" @tap="refresh"><text class="iconfont icon-shuaxin"></text>刷新</button>
					<!-- #endif -->
				</view>
				<view v-if="collage_uid == uid && status != 2" class="button-group">
					<button class="button" hover-class="none" @tap="onCancel">取消拼单</button>
					<!-- #ifdef MP -->
					<button class="button primary" hover-class="none" open-type="share">邀请好友</button>
					<!-- #endif -->
					<!-- #ifdef H5 -->
					<button v-if="$wechat.isWeixin()" class="button primary" hover-class="none" @tap="onShare">邀请好友</button>
					<!-- #endif -->
				</view>
			</view>
			<view v-if="collage_uid != uid" class="status">
				<view class="head">当前拼单进度</view>
				<view class="body">
					<view v-for="(item, index) in statusList" :key="item.value" :class="{ active: item.value == status }" class="item">
						<view class="number">{{ index + 1 }}</view>
						<view class="">{{ item.name }}</view>
					</view>
				</view>
			</view>
			<view v-if="collagePartake.length" class="choice">
				<view v-for="item in collagePartake" :key="item.userInfo.uid" class="wrapper">
					<view class="head">
						<view>
							<image class="avatar" :src="item.userInfo.avatar || '/static/images/f.png'" mode="aspectFill"></image>
						</view>
						<view class="name">{{ item.userInfo.nickname }}
							<text v-if="item.userInfo.uid == uid">（我）</text>
							<text v-else-if="item.userInfo.uid == collage_uid">（发起人）</text>
						</view>
						<template v-if="status != 2">
							<button v-if="item.userInfo.uid == uid && item.goods.length" class="button remove" hover-class="none" @tap="onRemove">清空</button>
							<button v-if="item.userInfo.uid == uid && item.goods.length" class="button" hover-class="none" @tap="onChange">修改</button>
							<button v-if="item.userInfo.uid != uid && item.goods.length" class="button" hover-class="none" @tap="onCopy(item.userInfo.uid)">跟TA选一样</button>
							<button v-if="item.userInfo.uid == uid && !item.goods.length" class="button select" hover-class="none" @tap="onChange">立即选购</button>
						</template>
					</view>
					<view v-if="item.goods.length" class="body">
						<view v-for="goods in item.goods" :key="goods.id" :class="{ gray: !goods.is_true_stock }" class="item">
							<view>
								<image class="image" :src="goods.productInfo.attrInfo.image" mode="aspectFill"></image>
							</view>
							<view class="text">
								<view class="name">{{ goods.productInfo.store_name }}</view>
								<view class="attr">{{ goods.productInfo.attrInfo.suk }}</view>
							</view>
							<view>
								<view>￥{{ goods.truePrice }}</view>
								<view class="number">x{{ goods.cart_num }}</view>
							</view>
						</view>
						<view class="total">小计<text class="money">￥{{ item.sumPrice }}</text></view>
					</view>
				</view>
			</view>
			<view v-if="collage_uid == uid && status != 2" class="footer">
				<view class="footer-wrap">
					<view class="total">最终优惠价:<text class="money">￥{{ total }}</text></view>
					<button class="button primary" hover-class="none" @tap="getCollagePartake(onSettle)">去结算</button>
				</view>
			</view>
		</template>
		<view v-else class="empty">
			<image class="image" :src="`${imgHost}/statics/images/no-thing.png`" mode="aspectFill"></image>
			<view class="tips">该订单已被发起人取消，返回重新选购</view>
			<button class="button" hover-class="none" @tap="goStore">返回门店</button>
		</view>
		<!-- 发送给朋友图片 -->
		<view class="share-box" v-if="H5ShareBox">
			<image :src="imgHost + '/statics/images/share-info.png'" @click="H5ShareBox = false">
			</image>
		</view>
		<!-- #ifdef MP -->
		<!-- <authorize v-if="isShowAuth" @authColse="authColse" @onLoadFun="onLoadFun"></authorize> -->
		<!-- #endif -->
	</view>
</template>

<script>
	import home from '@/components/home';
	import {
		getnearbyStore
	} from '@/api/new_store.js';
	import {
		getCollage,
		cancelCollage,
		settleCollage,
		getCollagePartake,
		emptyCollagePartake,
		duplicateCollagePartake
	} from '@/api/store.js'
	import colors from '@/mixins/color.js';
	import {
		mapGetters
	} from 'vuex';
	import {
		HTTP_REQUEST_URL
	} from '@/config/app';
	const themeMap = new Map([
		['#42ca4d', 'group-green.png'],
		['#e93323', 'group-red.png'],
		['#1db0fc', 'group-blue.png'],
		['#ff448f', 'group-pink.png'],
		['#fe5c2d', 'group-orange.png'],
		['#e0a558', 'group-golden.png'],
	]);
	export default {
		components: {
			home
		},
		mixins: [colors],
		data() {
			return {
				storeInfo: {},
				delivery_type: 2,
				where: {
					latitude: 0,
					longitude: 0,
					store_id: 0
				},
				collage_uid: 0,
				status: null,
				statusBarHeight: 0,
				navBarHeight: 0,
				collagePartake: [],
				isShowAuth: false,
				imgHost: HTTP_REQUEST_URL,
				statusList: [{
						name: '完成选购',
						value: 0,
					},
					{
						name: '提交订单',
						value: 1,
					},
					{
						name: '完成拼单',
						value: 2,
					}
				],
				H5ShareBox: false
			}
		},
		computed: {
			...mapGetters(['uid', 'isLogin']),
			total() {
				return this.collagePartake.reduce((total, {
					sum_price
				}) => {
					return this.$util.$h.Add(total, sum_price);
				}, 0);
			},
			header() {
				let colorStyle = this.colorStyle.split(';');
				let colorStyleObj = {};
				let header = '';
				colorStyle.forEach(item => {
					if (item) {
						item = item.split(':');
						colorStyleObj[item[0].trim()] = item[1].trim();
					}
				});
				header = themeMap.get(colorStyleObj['--view-theme'].toLocaleLowerCase())
				return `${HTTP_REQUEST_URL}/statics/images/${header}`;
			},
			plainPartake() {
				let partake = this.collagePartake.filter(item => {
					return item.goods.length;
				});
				return partake;
			},
			canCheckUser() {
				return {
					status: this.status,
					collagePartake: this.collagePartake
				}
			}
		},
		watch: {
			canCheckUser(value) {
				if (value.status == 2 || !value.collagePartake.length) {
					return;
				}
				let hasUser = false;
				for (let partakeItem of this.collagePartake) {
					hasUser = this.uid == partakeItem.userInfo.uid;
					if (hasUser) {
						break;
					}
				}
				if (!hasUser) {
					this.$util.Tips({
						title: '请去个人中心登录'
					});
				}
			}
		},
		onLoad(options) {
			this.collage_id = options.collage_id;
			// #ifdef MP
			let {
				top,
				height
			} = uni.getMenuButtonBoundingClientRect();
			uni.getSystemInfo({
				success: (res) => {
					const {
						statusBarHeight
					} = res;
					this.statusBarHeight = statusBarHeight;
					this.navBarHeight = (top - statusBarHeight) * 2 + height;
				}
			});
			// #endif
		},
		onShow() {
			if (this.isLogin) {
				this.getLocation();
			} else {
				toLogin();
			}
		},
		methods: {
			getLocation() {
				// #ifdef H5
				if (this.$wechat.isWeixin()) {
					this.$wechat.location().then(res => {
						try {
							uni.setStorageSync('user_latitude', res.latitude);
							uni.setStorageSync('user_longitude', res.longitude);
							this.getCollage();
							this.getCollagePartake();
						} catch (e) {
							//TODO handle the exception
						}
					}).catch(() => {
						this.getCollage();
						this.getCollagePartake();
					});
				}
				// #endif
				// #ifdef MP
				uni.getLocation({
					type: 'gcj02',
					success: (res) => {
						try {
							uni.setStorageSync('user_latitude', res.latitude);
							uni.setStorageSync('user_longitude', res.longitude);
						} catch (e) {
							//TODO handle the exception
						}
					},
					fail: (err) => {
						this.$util.Tips({
							title: err
						});
					},
					complete: () => {
						this.getCollage();
						this.getCollagePartake();
					}
				});
				// #endif
			},
			// 授权回调
			onLoadFun() {
				this.isShowAuth = false;
				this.getLocation();
			},
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			getCollage() {
				getCollage({
					collage_id: this.collage_id
				}).then(res => {
					const {
						shipping_type,
						address_id,
						status,
						uid,
						store_id
					} = res.data.collage;
					this.delivery_type = shipping_type;
					this.address_id = address_id;
					this.status = status;
					this.collage_uid = uid;
					this.where.store_id = store_id;
					this.getnearbyStore();
				});
			},
			// 获取门店信息
			getnearbyStore() {
				try {
					this.where.latitude = uni.getStorageSync('user_latitude');
					this.where.longitude = uni.getStorageSync('user_longitude');
				} catch (e) {
					//TODO handle the exception
				}
				getnearbyStore(this.where).then(res => {
					this.storeInfo = res.data.info;
					// #ifdef H5
					if (this.$wechat.isWeixin()) {
						let configAppMessage = {
							desc: this.storeInfo.detailed_address,
							title: this.storeInfo.name,
							link: window.location.href,
							imgUrl: this.header,
						};
						this.$wechat.wechatEvevt(['updateAppMessageShareData', 'updateTimelineShareData', 'onMenuShareAppMessage', 'onMenuShareTimeline'], configAppMessage);
					}
					// #endif
				});
			},
			// 拼单商品信息
			getCollagePartake(callback) {
				if (!callback) {
					uni.showLoading({
						title: '加载中'
					});
				}
				getCollagePartake({
					collage_id: this.collage_id
				}).then(res => {
					this.collagePartake = res.data;
					if (!callback) {
						return uni.hideLoading();
					}
					let is_true_stock = true;
					for (let partakeItem of this.collagePartake) {
						if (!is_true_stock) {
							break;
						}
						for (let goodsItem of partakeItem.goods) {
							is_true_stock = goodsItem.is_true_stock;
							if (!is_true_stock) {
								break;
							}
						}
					}
					if (!is_true_stock) {
						return this.$util.Tips({
							title: '存在库存不足的商品，请检查'
						});
					}
					callback();
				}).catch(() => {
					uni.hideLoading();
				});
			},
			// 清空
			onRemove() {
				emptyCollagePartake({
					collage_id: this.collage_id
				}).then(res => {
					this.$util.Tips({
						title: '清空成功',
						success: () => {
							this.getCollagePartake();
						}
					});
				});
			},
			// 修改、立即选购
			onChange() {
				uni.navigateTo({
					url: `/pages/store/group_cart/index?id=${this.where.store_id}&collage_id=${this.collage_id}&delivery_type=${this.delivery_type}`
				});
			},
			// 跟TA选一样
			onCopy(c_uid) {
				duplicateCollagePartake({
					collage_id: this.collage_id,
					c_uid
				}).then(res => {
					this.$util.Tips({
						title: '跟TA选一样成功',
						success: () => {
							this.getCollagePartake();
						}
					});
				}).catch(err => {
					this.$util.Tips({
						title: err
					});
				});
			},
			// 取消拼单
			onCancel() {
				cancelCollage({
					collage_id: this.collage_id
				}).then(res => {
					this.$util.Tips({
						title: '取消拼单成功'
					}, {
						tab: 3
					});
				});
			},
			// 去结算
			onSettle() {
				if (!this.plainPartake.length) {
					return this.$util.Tips({
						title: '暂无人点单'
					});
				}
				uni.showModal({
					title: '确定要结算吗？',
					content: '结算后，其他人无法再加入或修改商品',
					success: (res) => {
						if (res.confirm) {
							
							settleCollage({
								collage_id: this.collage_id
							}).then(res => {
								uni.navigateTo({
									url: `/pages/goods/order_confirm/index?new=1&collage_id=${this.collage_id}&addressId=${this.address_id}&cartId=${res.data.cartIds}&delivery_type=${this.delivery_type}&store_id=${this.where.store_id}&store_name=${this.storeInfo.name}`
								});
							}).catch(err => {
								this.$util.Tips({
									title: err
								});
							});
						}
					}
				});
			},
			goStore() {
				uni.reLaunch({
					url: `/pages/store_cate/store_cate?id=${this.where.store_id}`
				})
			},
			goBack() {
				let pages = getCurrentPages();
				if (pages.length > 1) {
					uni.navigateBack();
				} else {
					this.goStore();
				}
			},
			// 刷新
			refresh() {
				this.getCollage();
				this.getCollagePartake();
			},
			onShare() {
				this.H5ShareBox = true;
			}
		},
	}
</script>
<style>
	page {
		padding-bottom: 100rpx;
		padding-bottom: calc(100rpx + constant(safe-area-inset-bottom));
		padding-bottom: calc(100rpx + env(safe-area-inset-bottom));
		background-color: #F5F5F5;
	}
</style>
<style scoped lang="scss">
	.nav-bar {
		position: fixed;
		top: 0;
		right: 0;
		left: 0;
		z-index: 9;
		background-color: var(--view-theme);

		.content {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100rpx;
			font-size: 30rpx;
			color: #FFFFFF;
		}

		.button {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			display: flex;
			align-items: center;
			padding: 0 30rpx;
			background-color: transparent;
			font-size: 24rpx;
			color: #FFFFFF;
		}
	}

	.header {
		display: block;
		width: 100%;
		height: 320rpx;
	}

	.simple {
		position: relative;
		padding: 26rpx 26rpx 40rpx;
		border-radius: 14rpx;
		margin: -50rpx 30rpx 0;
		background-color: #FFFFFF;

		.seller {
			font-size: 24rpx;
			line-height: 32rpx;
			color: #999999;

			.head {
				display: flex;
				margin-bottom: 18rpx;
				color: #666666;
				
				.headCon{
					width: 500rpx;
				}
			}

			.tag {
				padding: 0 6rpx;
				border: 1rpx solid var(--view-theme);
				border-radius: 5rpx;
				font-size: 22rpx;
				line-height: 32rpx;
				height: 34rpx;
				color: var(--view-theme);
			}

			.name {
				flex: 1;
				min-width: 0;
				margin: 0 10rpx;
				font-weight: 500;
				font-size: 28rpx;
				color: #333333;
			}
		}

		.buyer {
			display: flex;
			align-items: center;
			margin-top: 34rpx;
			font-size: 26rpx;
			color: #888888;

			.avatar-group {
				margin-right: 10rpx;
				font-size: 0;

				.gengduo {
					display: inline-flex;
					justify-content: center;
					align-items: center;
					width: 40rpx;
					height: 40rpx;
					border-radius: 50%;
					margin-left: -12rpx;
					background-color: #EEEEEE;
					vertical-align: middle;

					.iconfont {
						font-size: 26rpx;
						color: #8E8E8E;
					}
				}
			}

			.avatar {
				width: 40rpx;
				height: 40rpx;
				border-radius: 50%;
				vertical-align: middle;

				+.avatar {
					margin-left: -12rpx;
				}
			}

			.button {
				display: flex;
				justify-content: center;
				align-items: center;
				width: 88rpx;
				height: 32rpx;
				border: 1px solid #CCCCCC;
				border-radius: 16rpx;
				margin-left: 20rpx;
				font-size: 20rpx;
				color: #666666;

				.iconfont {
					margin-right: 8rpx;
					font-size: 16rpx;
					color: #666666;
				}
			}
		}

		.button-group {
			display: flex;
			justify-content: center;
			margin-top: 34rpx;

			.button {
				display: flex;
				justify-content: center;
				align-items: center;
				width: 192rpx;
				height: 68rpx;
				border-radius: 34rpx;
				background-color: #EEEEEE;
				font-size: 26rpx;
				color: #666666;

				+.button {
					margin-left: 28rpx;
				}

				&.primary {
					background-color: var(--view-theme);
					color: #FFFFFF;
				}
			}
		}
	}

	.choice {
		.wrapper {
			border-radius: 14rpx;
			margin: 20rpx 30rpx;
			background-color: #FFFFFF;
		}

		.head {
			display: flex;
			align-items: center;
			height: 108rpx;
			padding: 0 26rpx;

			.name {
				flex: 1;
				min-width: 0;
				margin: 0 20rpx;
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
				font-size: 28rpx;
				color: #333333;
			}
		}

		.avatar {
			display: block;
			width: 42rpx;
			height: 42rpx;
			border-radius: 50%;
		}

		.button {
			transform: rotateZ(360deg);
			padding: 15rpx 26rpx;
			border: 1rpx solid #CCCCCC;
			border-radius: 26rpx;
			font-size: 24rpx;
			line-height: 24rpx;
			color: #333333;

			+.button {
				margin-left: 10rpx;
			}
			
			&.remove {
				color: #888888;
			}
			
			&.select {
				border-color: var(--view-theme);
				background-color: var(--view-minorColorT);
				color: var(--view-theme);
			}

			&.primary {
				border-color: var(--view-theme);
				background-color: var(--view-minorColorT);
				color: var(--view-theme);
			}
		}

		.body {
			position: relative;
			padding: 15rpx 0;
			font-size: 28rpx;
			line-height: 42rpx;
			color: #333333;
			
			&::before {
				content: "";
				position: absolute;
				top: 0;
				right: 26rpx;
				left: 26rpx;
				border-top: 1rpx solid #EEEEEE;
			}

			.text {
				flex: 1;
				min-width: 0;
				margin: 0 22rpx;
				
				.name {
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					font-size: 28rpx;
				}
				
				.attr {
					margin-top: 8rpx;
					font-size: 20rpx;
					color: #999999;
				}
			}
		}

		.item {
			display: flex;
			padding: 15rpx 26rpx;

			+.item {
				// margin-top: 30rpx;
			}
			
			&.gray {
				background-color: #EEEEEE;
			}
		}

		.image {
			width: 120rpx;
			height: 120rpx;
			border-radius: 6rpx;
		}

		.number {
			text-align: right;
			color: #999999;
		}

		.total {
			display: flex;
			justify-content: flex-end;
			align-items: center;
			padding: 0 26rpx;
			margin-top: 30rpx;
		}

		.money {
			font-weight: 600;
			color: var(--view-theme);
		}
	}

	.footer {
		position: fixed;
		right: 0;
		bottom: 0;
		left: 0;
		z-index: 9;
		padding-bottom: constant(safe-area-inset-bottom);
		padding-bottom: env(safe-area-inset-bottom);
		background-color: #FFFFFF;

		.footer-wrap {
			display: flex;
			align-items: center;
			height: 100rpx;
			padding: 0 30rpx 0 26rpx;
		}

		.total {
			flex: 1;
			min-width: 0;
			font-size: 28rpx;
			color: #333333;
		}

		.money {
			color: var(--view-theme);
		}

		.button {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 192rpx;
			height: 70rpx;
			border-radius: 35rpx;
			background-color: var(--view-theme);
			font-size: 28rpx;
			color: #FFFFFF;
		}
	}

	.status {
		padding: 34rpx 0 32rpx;
		border-radius: 14rpx;
		margin: 20rpx 30rpx;
		background-color: #FFFFFF;

		.head {
			display: flex;
			justify-content: center;
			align-items: center;
			font-size: 28rpx;
			line-height: 28rpx;
			color: #333333;
		}

		.body {
			display: flex;

			.item {
				flex: 1;
				min-width: 0;
				margin-top: 40rpx;
				text-align: center;
				font-size: 24rpx;
				line-height: 24rpx;
				color: #888888;

				&:first-child .number::before {
					display: none;
				}

				&.active {
					color: #333333;

					.number {
						border-color: var(--view-theme);
						background-color: var(--view-theme);
						color: #FFFFFF;
					}
				}
			}

			.number {
				position: relative;
				display: inline-flex;
				justify-content: center;
				align-items: center;
				width: 36rpx;
				height: 36rpx;
				border: 1rpx solid #CCCCCC;
				border-radius: 50%;
				margin-bottom: 20rpx;

				&::before {
					content: "";
					position: absolute;
					top: 50%;
					right: 100%;
					width: 160rpx;
					height: 1rpx;
					border-top: 1rpx dashed #D8D8D8;
					margin-right: 18rpx;
				}
			}
		}
	}

	.empty {
		height: 928rpx;
		padding-top: 120rpx;
		border-radius: 12rpx;
		margin: -50rpx 30rpx 40rpx;
		background-color: #FFFFFF;

		.image {
			display: block;
			width: 414rpx;
			height: 256rpx;
			margin: 0 auto;
		}

		.tips {
			margin-top: 54rpx;
			text-align: center;
			font-size: 26rpx;
			line-height: 36rpx;
			color: #999999;
		}

		.button {
			transform: rotateZ(360deg);
			display: flex;
			justify-content: center;
			align-items: center;
			width: 507rpx;
			height: 85rpx;
			border: 1rpx solid var(--view-theme);
			border-radius: 43rpx;
			margin: 160rpx auto 0;
			font-size: 30rpx;
			color: var(--view-theme);
		}
	}

	.share-box {
		z-index: 1000;
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;

		image {
			width: 100%;
			height: 100%;
		}
	}
</style>