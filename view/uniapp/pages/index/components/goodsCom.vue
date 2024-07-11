<template>
	<view class="box">
		<view class="title">
			<text>{{item.cate_name}}</text>
			<view class="more" @click="gotoList(item.id)">
				<text>更多</text>
				<image src="/static/images/check_more.png" mode="widthFix"></image>
			</view>
		</view>
		
		<view class="cate">
			<scroll-view scroll-x enable-flex class="cate-list">
				<view class="cate-item" v-for="(item, index) in item.children" :key="item.id" :class="index == active ? 'active' : ''" @click="swtichCate(index)">
					{{item.cate_name}}
				</view>
			</scroll-view>
		</view>
		
		<scroll-view  scroll-x :scroll-into-view="scrollIndex" @scroll="onScroll">
			<view class="list">
				<view class="item" v-for="(item, i) in goodsList" :key="item.product_id" :id="'scroll'+index+i" @click="goto(item.product_id)">
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
			},
			index: {
				type: Number,
				default: () => 0
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
				this.scrollIndex = 'scroll' + this.index + index
				this.active = index
			},
			onScroll({ detail }) {
				const offsetLeft = detail.scrollLeft; // 获取滚动条相对于顶部的偏移量
				this.active = Math.floor(offsetLeft * 2 / 430); // 根据偏移量计算当前所处的索引
				// this.scrollIndex = 'scroll' + this.active
			},
			goto(id) {
				uni.navigateTo({
					url: `/pages/class_details/index?id=${id}`
					// url: `/pages/goods_details/index?id=${id}`
				})
			},
			gotoList(id) {
				uni.navigateTo({
					url: `/pages/classes/list/list?id=${id}`
				})
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
		overflow: hidden;
		
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
			padding-right: 0;
			
			.item {
				padding-left: 30rpx;
				width: 400rpx;
				box-sizing: content-box;
				
				&:last-child {
					padding-right: 30rpx;
				}
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
						text-overflow: ellipsis;
						overflow: hidden;
						white-space: nowrap;
						display: block;
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
					height: 34rpx;
				}
			}
		}
		
		.cate {
			margin-bottom: 20rpx;
			// overflow-x: auto;
			.cate-list {
				display: flex;
				padding: 0 30rpx;
				height: 60rpx;
				
				.cate-item {
					margin-right: 30rpx;
					white-space: nowrap;
					height: 44rpx;
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