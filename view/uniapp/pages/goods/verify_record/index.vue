<template>
	<view :style="colorStyle">
		<view v-if="records.length" class="verify-wrapper">
			<view v-for="section in records" :key="section.time" class="section">
				<view class="head">{{ section.time }}</view>
				<view class="body">
					<view v-for="item in section.list" :key="item.id" class="item">
						<image class="image" :src="item.cartInfo.attrInfo.image" mode=""></image>
						<view class="detail">
							<view class="title">{{ item.cartInfo.productInfo.store_name }}</view>
							<view class="attribute">{{ item.cartInfo.attrInfo.suk }}</view>
							<view class="money">￥{{ item.cartInfo.attrInfo.price }}</view>
						</view>
						<view class="count">x {{ item.writeoff_num }}</view>
					</view>
				</view>
			</view>
		</view>
		<empty-page v-else-if="loadend" title="暂无核销记录哦～"></empty-page>
		<view class='loadingicon acea-row row-center-wrapper'>
			<text class='loading iconfont icon-jiazai' :hidden="!loading"></text>
			<template v-if="!loading && records.length">{{ loadend ? '没有更多内容啦~' : '加载更多' }}</template>
		</view>
		<home v-if="navigation"></home>
	</view>
</template>

<script>
	import home from '@/components/home/index.vue';
	import emptyPage from '@/components/emptyPage.vue';
	import color from '@/mixins/color.js';
	import {
		orderWriteRecords
	} from '@/api/order.js';

	export default {
		components: {
			home,
			emptyPage
		},
		mixins: [color],
		data() {
			return {
				oid: 0,
				page: 1,
				limit: 20,
				loading: false,
				loadend: false,
				list: [],
				time: [],
				records: []
			}
		},
		onLoad(options) {
			this.oid = options.oid;
			this.orderWriteRecords();
		},
		onReachBottom() {
			this.orderWriteRecords();
		},
		methods: {
			orderWriteRecords() {
				if (this.loading || this.loadend) {
					return;
				}
				this.loading = true;
				orderWriteRecords(this.oid, {
					page: this.page++,
					limit: this.limit
				}).then(res => {
					let {
						count,
						list,
						time
					} = res.data;
					this.list = [...this.list, ...list];
					this.time = [...new Set([...this.time, ...time])];
					this.records = this.time.map(time => ({
						time,
						list: this.list.filter(item => item.time_key === time)
					}));
					this.loading = false;
					this.loadend = this.list.length === count;
				}).catch(err => {
					this.loading = false;
					this.$util.Tips({
						title: err
					});
				});
			}
		},
	}
</script>

<style lang="scss" scoped>
	.verify-wrapper {
		.section {
			background-color: #FFFFFF;

			+.section {
				margin-top: 20rpx;
			}
		}

		.head {
			display: flex;
			align-items: center;
			padding: 22rpx 30rpx;
			font-size: 28rpx;
			line-height: 42rpx;
			color: #999999;
		}

		.body {
			padding-left: 30rpx;
			border-top: 1rpx solid #F0F0F0;
		}

		.item {
			display: flex;
			padding: 25rpx 30rpx 25rpx 0;

			+.item {
				border-top: 1rpx solid #F0F0F0;
			}
		}

		.image {
			width: 130rpx;
			height: 130rpx;
		}

		.detail {
			flex: 1;
			min-width: 0;
			padding: 0 22rpx;

			.title {
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
				font-size: 28rpx;
				line-height: 40rpx;
				color: #333333;
			}

			.attribute {
				margin-top: 8rpx;
				font-size: 20rpx;
				line-height: 28rpx;
				color: #999999;
			}

			.money {
				margin-top: 18rpx;
				font-size: 26rpx;
				line-height: 32rpx;
				color: var(--view-priceColor);
			}
		}

		.count {
			font-size: 26rpx;
			line-height: 40rpx;
			color: #999999;
		}
	}
</style>