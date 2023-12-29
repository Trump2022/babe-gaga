<template>
	<view :style="colorStyle">
		<view :style="{ backgroundPositionY: `${statusBarHeight + navBarHeight}px` }" class="confirm-order">
			<view class="nav-bar fixed">
				<view :style="{ height: `${statusBarHeight}px` }"></view>
				<view :style="{ height: `${navBarHeight}px` }" class="inner">
					<navigator class="btn" open-type="navigateBack" hover-class="none">
						<text class="iconfont icon-fanhui2"></text>
					</navigator>
					<view>提交订单</view>
				</view>
			</view>
			<view class="header">
				<view class="top">
					<view class="name">{{ codeData.storeName.storeName }}</view>
					<view class="number"><text class="light">{{ codeData.category.name }}{{ codeData.table_number }}</text>号桌</view>
				</view>
				<view>{{ codeData.serial_number }}</view>
			</view>
			<view class="dining">
				<view class="total">共点{{ goodsNumber }}份</view>
				<view>{{ tablePartake.length }}人就餐</view>
			</view>
			<view v-for="item in tablePartake" :key="item.userInfo.uid" class="dinner">
				<view class="head">
					<image class="image" :src="item.userInfo.avatar || '/static/images/f.png'" mode="aspectFill"></image>
					<view class="name">{{ item.userInfo.nickname }}</view>
					<!-- <view class="name">已点7份</view> -->
					<view>{{ item.order_time }}下单</view>
				</view>
				<view class="body">
					<view v-for="goods in item.goods" :key="goods.id" :class="{ gray: !goods.is_true_stock }" class="item">
						<image class="image" :src="goods.productInfo.image" mode="aspectFill"></image>
						<view class="text">
							<view class="name">{{ goods.productInfo.store_name }}</view>
							<view class="attr">{{ goods.productInfo.attrInfo.suk }}</view>
							<view class="money">￥{{ goods.truePrice }}</view>
						</view>
						<view class="number">x {{ goods.cart_num }}</view>
					</view>
				</view>
			</view>
			<view class="footer">
				<view class="inner"></view>
			</view>
			<view class="footer fixed">
				<view class="inner">
					<view class="total">合计:<text class="money">￥{{ total }}</text></view>
					<button class="btn" hover-class="none" @tap="getTableCode(onAdd)">加购</button>
					<button class="btn fill" hover-class="none" @tap="getTableCode(onSettle)">去结账</button>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import colors from '@/mixins/color.js';
	import {
		getCodeData,
		getTableCode,
		getTablePartake,
		settleTable,
	} from '@/api/store.js';

	export default {
		mixins: [colors],
		data() {
			return {
				statusBarHeight: 0,
				navBarHeight: 0,
				tablePartake: [],
				codeData: {},
			}
		},
		computed: {
			goodsNumber() {
				let goodsNumber = 0;
				this.tablePartake.forEach(item => {
					goodsNumber = item.goods.reduce((total, cell) => {
						return total + cell.cart_num;
					}, goodsNumber);
				})
				return goodsNumber;
			},
			total() {
				let total = 0;
				this.tablePartake.forEach(item => {
					total = item.goods.reduce((total, cell) => {
						return this.$util.$h.Add(total, this.$util.$h.Mul(cell.truePrice, cell.cart_num));
					}, total);
				});
				return total;
			}
		},
		onLoad(option) {
			const {
				tableId,
			} = option;
			let {
				height,
				top
			} = uni.getMenuButtonBoundingClientRect();
			uni.getSystemInfo({
				success: ({
					statusBarHeight
				}) => {
					this.statusBarHeight = statusBarHeight;
					this.navBarHeight = 2 * (top - statusBarHeight) + height;
				}
			});
			this.tableId = tableId;
			this.getCodeData();
			this.getTablePartake();
		},
		onPullDownRefresh() {
			this.getTablePartake();
		},
		methods: {
			// 获取桌码信息
			getCodeData() {
				getCodeData({
					tableId: this.tableId
				}).then(res => {
					this.codeData = res.data;
				});
			},
			getTablePartake(callback) {
				getTablePartake({
					tableId: this.tableId
				}).then(({
					data
				}) => {
					let tablePartake = [];
					Object.keys(data).forEach(key => {
						tablePartake.push(data[key]);
					});
					this.tablePartake = tablePartake;
					uni.stopPullDownRefresh();
					if (callback) {
						let is_true_stock = true;
						for (let partakeItem of this.tablePartake) {
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
					}
				}).catch(err => {
					this.$util.Tips({
						title: err
					});
				});
			},
			settleTable() {
				settleTable({
					tableId: this.tableId
				}).then(res => {
					uni.navigateTo({
						url: `/pages/goods/order_confirm/index?new=1&delivery_type=2&cartId=${res.data.cartIds}&store_id=${this.codeData.store_id}&tableId=${this.tableId}`
					});
				}).catch(err => {
					this.$util.Tips({
						title: err
					});
				});
			},
			// 去结账
			onSettle() {
				uni.showModal({
					title: '是否结账',
					content: '您是否确认结算当前账单',
					success: ({
						confirm
					}) => {
						if (confirm) {
							this.getTablePartake(this.settleTable);
						}
					}
				});
			},
			// 加购
			onAdd() {
				uni.navigateTo({
					url: `/pages/store/table_code/index?store_id=${this.codeData.store_id}&qrcode_id=${this.codeData.id}`
				});
			},
			getTableCode(callback) {
				getTableCode({ tableId: this.tableId }).then(res => {
					const status = res.data.table.status;
					switch (status){
						case -1:
						case 2:
						case 3:
							this.$util.Tips({
								title: status == -1 ? '本单已取消' : '本单已结账'
							});
							break;
						default:
							callback();
							break;
					}
				}).catch(err => {
					this.$util.Tips({
						title: err
					});
				});
			}
		},
	}
</script>

<style>
	page {
		background-color: #F5F5F5;
	}
</style>

<style lang="scss" scoped>
	.confirm-order {
		background: linear-gradient(180deg, var(--view-theme) 0%, #F5F5F5 100%);
		background-size: 100% 292rpx;
		background-repeat: no-repeat;
	}

	.nav-bar {
		background-color: var(--view-theme);

		&.fixed {
			position: sticky;
			top: 0;
			right: 0;
			left: 0;
			z-index: 9;
		}

		.inner {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			font-size: 30rpx;
			color: #FFFFFF;
		}

		.btn {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			display: flex;
			align-items: center;
			padding: 0 30rpx;
			font-size: 24rpx;
			color: #FFFFFF;
		}
	}

	.header {
		padding: 52rpx 24rpx 46rpx;
		border-radius: 14rpx;
		margin: 16rpx 30rpx 20rpx;
		background-color: #FFFFFF;
		font-size: 24rpx;
		color: #999999;

		.top {
			display: flex;
			margin-bottom: 18rpx;
		}

		.name {
			flex: 1;
			min-width: 0;
			font-weight: 600;
			font-size: 34rpx;
			color: #333333;
		}

		.number {
			color: #333333;
		}

		.light {
			margin-right: 6rpx;
			font-weight: 600;
			font-size: 34rpx;
			color: var(--view-theme);
		}
	}

	.dining {
		display: flex;
		align-items: center;
		height: 85rpx;
		padding: 0 24rpx;
		border-radius: 14rpx;
		margin: 20rpx 30rpx;
		background-color: #FFFFFF;
		font-size: 24rpx;
		color: #333333;

		.total {
			flex: 1;
			min-width: 0;
		}
	}

	.dinner {
		border-radius: 14rpx;
		margin: 20rpx 30rpx;
		background-color: #FFFFFF;

		.head {
			display: flex;
			align-items: center;
			height: 86rpx;
			padding: 0 25rpx;
			border-bottom: 1rpx solid #F0F0F0;
			font-size: 26rpx;
			color: #333333;

			.image {
				display: block;
				width: 40rpx;
				height: 40rpx;
				border-radius: 50%;
			}

			.name {
				flex: 1;
				min-width: 0;
				margin: 0 20rpx;
			}
		}

		.item {
			position: relative;
			display: flex;
			padding: 25rpx;
			
			&.gray {
				background-color: #EEEEEE;
			}

			+.item {
				&::before {
					content: "";
					position: absolute;
					top: 0;
					right: 0;
					left: 25rpx;
					border-top: 1rpx solid #EEEEEE;
				}
			}

			.image {
				display: block;
				width: 130rpx;
				height: 130rpx;
				border-radius: 6rpx;
			}

			.text {
				flex: 1;
				min-width: 0;
				margin: 0 20rpx;
			}

			.name {
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
				font-size: 28rpx;
				line-height: 40rpx;
				color: #333333;
			}

			.attr {
				margin-top: 8rpx;
				font-size: 20rpx;
				color: #999999;
			}

			.money {
				margin-top: 14rpx;
				font-size: 26rpx;
				line-height: 36rpx;
				color: var(--view-theme);
			}

			.number {
				font-size: 26rpx;
				line-height: 40rpx;
				color: #999999;
			}
		}
	}

	.footer {
		padding-bottom: constant(safe-area-inset-bottom);
		padding-bottom: env(safe-area-inset-bottom);

		&.fixed {
			position: fixed;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 9;
			background-color: #FFFFFF;
		}

		.inner {
			display: flex;
			align-items: center;
			height: 100rpx;
			padding-right: 20rpx;
		}

		.total {
			flex: 1;
			min-width: 0;
			padding: 0 26rpx;
			font-size: 28rpx;
			color: #333333;
		}

		.money {
			color: var(--view-theme);
		}

		.btn {
			transform: rotateZ(360deg);
			padding: 18rpx 62rpx;
			border: 1rpx solid var(--view-theme);
			border-radius: 38rpx;
			font-size: 28rpx;
			line-height: 40rpx;
			color: var(--view-theme);

			+.btn {
				margin-left: 20rpx;
			}

			&.fill {
				background-color: var(--view-theme);
				color: #FFFFFF;
			}
		}
	}
</style>