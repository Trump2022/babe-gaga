<template>
	<view class="box">
		<view class="title">
			<text>{{item.cate_name}}</text>
			<view class="more">
				<text>更多</text>
				<image src="/static/images/check_more.png" mode="widthFix"></image>
			</view>
		</view>
		
		<view class="cate">
			<view class="cate-list">
				<view class="cate-item" v-for="(item, index) in item.children" :key="item.id" :class="index == active ? 'active' : ''" @click="swtichCate(index)">
					{{item.cate_name}}
				</view>
			</view>
		</view>
		
		<scroll-view  scroll-x :scroll-into-view="scrollIndex">
			<view class="list">
				<view class="item" v-for="(item, index) in goodsList" :key="item.id" :id="'scroll'+index">
					<image :src="item.image"></image>
					<view class="text-box">
						<text class="name">{{item.store_name}}</text>
					</view>
					<view class="store-info">
						{{item.store_info}}
					</view>
					<view class="text-box name-box">
						<text class="price">
							<text>￥</text>
							{{item.price}}
						</text>
						<text class="ot_price">{{item.ot_price}}</text>
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
		props: {
			item: {
				type: Object,
				default: () => {}
			}
		},
		data() {
			return {
				location: {
					latitude: 0,
					longitude: 0
				},
				list: [],
				active: 0,
				goodsList: this.item.children.reduce((t, item) => {
					if(item.product_list.length > 0) {
						t.push(...item.product_list)
					}
					return t
				}, []),
				scrollIndex: 'scroll' + 0
			}
		},
		mounted() {
			
		},
		methods: {
			swtichCate(index) {
				this.scrollIndex = 'scroll' + index
				this.active = index
			}
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
			
			.item {
				margin-right: 30rpx;
				width: 400rpx;
				image {
					width: 400rpx;
					height: 400rpx;
					border-radius: 12rpx;
				}
				
				.text-box {
					.name {
						color: #231815;
						font-weight: bold;
						font-size: 34rpx;
					}
					
					text {
						font-size: 24rpx;
						color: #97A1AB;
					}
					
					.price {
						color: #FB4095;
						font-size: 42rpx;
						font-weight: bold;
						margin-right: 18rpx;
						
						text {
							font-size: 24rpx;
							font-weight: normal;
						}
					}
					
					.ot_price {
						color: #97A1AB;
						font-weight: bold;
						font-size: 26rpx;
						text-decoration: line-through;
					}
				}
				
				.name-box {
					margin: 18rpx 0 6rpx;
				}
				
				.store-info {
					width: 100%;
					white-space: nowrap;
					overflow: hidden;
					text-overflow: ellipsis;
					font-size: 24rpx;
					color: #97A1AB;
				}
			}
			
			
		}
		
		.cate {
			margin-bottom: 36rpx;
			// overflow-x: auto;
			.cate-list {
				display: flex;
				padding: 0 30rpx;
				
				.cate-item {
					margin-right: 30rpx;
				}
				
				.active {
					color: #FB4095;
					position: relative;
					
					&::after {
						content: '';
						display: block;
						position: absolute;
						bottom: -14rpx;
						left: 50%;
						transform: translateX(-50%);
						background-image: url('/static/images/home_active.png');
						background-size: 100% 100%;
						width: 30rpx;
						height: 10rpx
					}
				}
			}
		}
	}
</style>