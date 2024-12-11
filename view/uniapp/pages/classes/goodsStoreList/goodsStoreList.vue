<template>
	<view :style="colorStyle" class="store-box" v-if="storeNewObj.list && storeNewObj.list.length > 0">
		<view class="store-list">
			<view class='address' v-for="item in storeNewObj.list" :key="item.id" @tap.stop="makePhone(item.phone)">
				<view class='name acea-row'>
					<view class="nameCon">{{item.name}}</view>
					<text class='phone'>{{item.phone}}</text>
					<text class="iconfont icon-tonghua font-num"></text>
				</view>
				<view class='name acea-row'>
					{{item.day_time}}
				</view>
				<view class="navigation" @tap.stop="showMapLocation(item)">
					<text>{{item.detailed_address}}</text>
					<view>
						导航
						<text class="iconfont icon-xiangyou"></text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		goodsStoreList
	} from '@/api/store.js';
	import colors from "@/mixins/color";
	export default {
		mixins: [colors],
		data() {
			return {
				storeNewObj: {},
				id: 0
			}
		},
		onLoad(opt) {
			this.id = opt.id
			this.getStoreListFn()
		},
		methods: {
			getStoreListFn() {
				goodsStoreList({
					product_id: this.id,
					limit: 1000,
					page: 1
				}).then(res => {
					this.storeNewObj = res.data
				})
			},
			/**
			 * 拨打电话
			 */
			makePhone: function(phone) {
				uni.makePhoneCall({
					phoneNumber: phone
				});
			},
			showMapLocation(item) {
				if (!item.latitude || !item.longitude) return this.$util.Tips({
					title: '缺少经纬度信息无法查看地图！'
				});
				uni.openLocation({
					latitude: parseFloat(item.latitude),
					longitude: parseFloat(item.longitude),
					scale: 8,
					name: item.name,
					address: item.address + item.detailed_address,
					success: function() {
				
					},
				});
			},
		}
	}
</script>

<style lang="scss" scoped>
	.store-box {
		margin-top: 13rpx;
		background-color: #fff;
		
		.store-list {
			.address {
				margin-top: 0;
				font-size: 26rpx;
				background-color: #fff;
				padding: 35rpx 30rpx;
				border-top: 1rpx solid #f5f5f5;
				
				.name {
					font-size: 30rpx;
					color: #282828;
					margin-bottom: 15rpx;
					
					.nameCon {
						max-width: 410rpx;
						font-weight: bold;
					}
					
					.phone {
						margin-left: 40rpx;
					}
					
					.iconfont {
						font-size: 34rpx;
						margin-left: 10rpx;
						margin-top: 4rpx;
					}
				}
				
				
				.navigation {
					display: flex;
					justify-content: space-between;
					align-items: flex-start;
					width: 100%;
					font-size: 28rpx;
					
					view {
						width: 100rpx;
						flex-shrink: 0;
						color: #868686;
						margin-left: 30rpx;
						justify-content: space-around;
						display: flex;
						align-items: center;
					}
				}
			}
		}
	}
</style>