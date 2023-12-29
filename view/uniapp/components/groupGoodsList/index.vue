<template>
	<view v-if="goodsList.length" class="goods-list">
		<view v-for="item in goodsList" class="item">
			<view class="head">
				<image class="avatar" :src="item.userInfo.avatar || '/static/images/f.png'" mode="aspectFill"></image>
				<view class="name-wrap">
					<view class="name-inner">
						<view class="name">{{ item.userInfo.nickname }}</view>
						<view v-if="item.userInfo.uid == uid">（我）</view>
					</view>
				</view>
			</view>
			<view class="body">
				<view v-for="cell in item.goods" :key="cell.id" class="cell">
					<image class="image" :src="cell.productInfo.attrInfo.image" mode="aspectFill"></image>
					<view class="name-wrap">
						<view class="name">{{ cell.productInfo.store_name }}</view>
						<view class="attr">{{ cell.productInfo.attrInfo.suk }}</view>
						<view class="money">￥{{ cell.truePrice }}</view>
					</view>
					<view class="">x {{ cell.cart_num }}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapGetters
	} from 'vuex';

	export default {
		props: {
			goodsList: {
				type: Array,
				default: () => {
					return [];
				}
			},
		},
		computed: mapGetters(['uid']),
	};
</script>

<style lang="scss" scoped>
	.goods-list {
		.item {
			border-radius: 14rpx;
			background-color: #FFFFFF;

			+.item {
				margin-top: 20rpx;
			}

			.head {
				display: flex;
				align-items: center;
				height: 86rpx;
				padding: 0 26rpx;
				border-bottom: 1rpx solid #F0F0F0;
				font-size: 26rpx;
				color: #333333;

				.name-wrap {
					flex: 1;
					min-width: 0;
					margin-left: 20rpx;
				}

				.name-inner {
					display: inline-flex;
					align-items: center;
					max-width: 100%;
				}

				.name {
					flex: 1;
					min-width: 0;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
				}
			}

			.avatar {
				display: block;
				width: 40rpx;
				height: 40rpx;
				border-radius: 50%;
			}

			.body {
				padding-left: 24rpx;

				.cell {
					display: flex;
					padding: 24rpx 26rpx 26rpx 0;
					font-size: 26rpx;
					line-height: 40rpx;
					color: #999999;

					+.cell {
						border-top: 1rpx solid #EEEEEE;
					}

					.name-wrap {
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
						line-height: 28rpx;
					}

					.money {
						margin-top: 14rpx;
						line-height: 36rpx;
						color: #E93323;
					}
				}

				.image {
					display: block;
					width: 130rpx;
					height: 130rpx;
					border-radius: 6rpx;
				}
			}
		}
	}
</style>
