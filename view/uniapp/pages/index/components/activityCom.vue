<template>
	<view class="box">
		<view class="title">
			<view class="title-left">
				<text>定制活动</text>
				<text class="vip">[仅限会员]</text>
			</view>
			<view class="more" @click="gotoList">
				<text>更多</text>
				<image src="/static/images/check_more.png" mode="widthFix"></image>
			</view>
		</view>
		
		<view class="cate">
			<scroll-view scroll-x enable-flex class="cate-list">
				<view class="cate-item" v-for="(item, index) in activityCategoryList" :key="item.id" :class="index == active ? 'active' : ''" @click="swtichCate(index)">
					{{item.title}}
				</view>
			</scroll-view>
		</view>
		
		<scroll-view  scroll-x :scroll-into-view="scrollIndex" style="width: 714rpx;" @scroll="onScroll" v-if="activity_list.length">
			<view class="list">
				<view class="item" v-for="(item, index) in activity_list" :key="item.id" :id="'scroll'+index" @click="goto(item.id)">
					<image :src="item.image_input[0]"></image>
					<view class="text-box">
						<text class="name">{{item.title}}</text>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import {
		activityCategory
	} from '@/api/store.js';
	import {
		storeList
	} from '@/api/store.js';
	export default {
		data() {
			return {
				active: 0,
				scrollIndex: 'scroll' + 0,
				activityCategoryList: [],
				activity_list: []
			}
		},
		mounted() {
			this.getActivityCategory()
		},
		methods: {
			getActivityCategory() {
				activityCategory({is_class: 1}).then(res => {
					this.activityCategoryList = res.data
					
					this.activity_list = this.activityCategoryList.reduce((t, item) => {
						if(item.activity_list.length > 0) {
							t.push(...item.activity_list)
						}
						return t
					}, [])
				})
			},
			swtichCate(index) {
				this.active = index
				this.$nextTick(() => {
					this.scrollIndex = 'scroll' + index
				})
			},
			onScroll({ detail }) {
				const offsetLeft = detail.scrollLeft; // 获取滚动条相对于顶部的偏移量
				this.active = Math.floor(offsetLeft * 2 / 530); // 根据偏移量计算当前所处的索引
				// this.scrollIndex = 'scroll' + this.active
			},
			goto(id) {
				uni.navigateTo({
					url: '/pages/vip_activity/activityDetail/activityDetail?id=' + id
				})
			},
			gotoList() {
				uni.navigateTo({
					url: '/pages/vip_activity/activityList/activityList'
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
			
			.title-left {
				display: flex;
				align-items: center;
				
				.vip {
					color: #FCBBDB;
					font-size: 26rpx;
					margin-left: 18rpx;
					font-weight: normal;
				}
			}
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
			// overflow-x: auto;
			padding-right: 0;
			
			.item {
				padding-left: 30rpx;
				width: 500rpx;
				box-sizing: content-box;
				
				&:last-child {
					padding-right: 30rpx;
				}
				
				image {
					width: 500rpx;
					height: 280rpx;
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