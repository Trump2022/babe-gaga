<template>
	<view>
		<view class="recordList">
			<view class="item" v-for="(item,index) in list" :key="index">
				<view class="top">
					<view class="title">{{item.system_name}}</view>
					<view class="time">提交时间：{{item.add_time}}</view>
					<view class="reason" v-if="item.status==2">原因：{{item.fail_msg}}</view>
				</view>
				<view class="bottom acea-row row-between-wrapper">
					<view class="acea-row row-middle"><text class="iconfont" :class="item.status==1?'icon-shenhetongguo':item.status==2?'icon-shenheweitongguo':'icon-daishenhe'"></text>{{item.status==1?'审核通过':item.status==2?'审核未通过':'待审核'}}</view>
					<view class="bnt acea-row row-center-wrapper" @click="lookUp(item)" v-if="item.status==1">查看</view>
					<view class="bnt acea-row row-center-wrapper" @click="resubmit(item)" v-else-if="item.status==2">重新提交</view>
					<view class="bnt acea-row row-center-wrapper" @click="edit(item)" v-else>编辑</view>
				</view>
			</view>
		</view>
		<view class="emptyPage" v-if="list.length == 0">
			<emptyPage title="暂无申请记录~"></emptyPage>
		</view>
		<home v-if="navigation"></home>
	</view>
</template>

<script>
	import { recordList } from '@/api/user.js';
	import colors from '@/mixins/color.js';
	import emptyPage from '@/components/emptyPage.vue';
	import home from '@/components/home';
	export default{
		mixins: [colors],
		components: {
		   emptyPage,
		   home
		},
		data(){
			return{
				list:[]
			}
		},
		onLoad(){
			this.getList();
		},
		methods:{
			getList(){
				recordList().then(res=>{
					this.list = res.data;
				}).catch(err=>{
					return this.$util.Tips({
						title: err
					});
				})
			},
			edit(item){
				uni.reLaunch({
					url: '/pages/users/supplier/index?id='+ item.id
				})
			},
			resubmit(item){
				uni.navigateTo({
					url: '/pages/users/supplier_state/index?id='+ item.id+'&type=2'
				})
			},
			lookUp(item){
				uni.navigateTo({
					url: '/pages/users/supplier_state/index?id='+ item.id+'&type=1'
				})
			}
		}
	}
</script>

<style lang="scss">
	.emptyPage{
		padding-top: 1rpx;
	}
	.recordList{
		.item{
			width: 690rpx;
			background-color: #fff;
			border-radius: 12rpx;
			margin: 20rpx auto 0 auto;
			padding: 0 24rpx;
			.top{
				min-height: 180rpx;
				border-bottom: 1px solid #EEEEEE;
				padding-top: 1rpx;
				font-size: 24rpx;
				.title{
					font-size: 28rpx;
					color: #282828;
					margin-top: 35rpx;
				}
				.time{
					color: #999999;
					margin-top: 15rpx;
				}
				.reason{
					color: #E93323;
					margin-top: 10rpx;
				}
			}
			.bottom{
				height: 96rpx;
				.icon-daishenhe{
					font-size: 38rpx;
					margin: 4rpx 8rpx 0 0;
					color: #3382F7;
				}
				.icon-shenhetongguo{
					font-size: 36rpx;
					margin: 4rpx 8rpx 0 0;
					color: #61CE74;
				}
				.icon-shenheweitongguo{
					font-size: 36rpx;
					margin: 4rpx 8rpx 0 0;
					color: #F0441C;
				}
				.bnt{
					border: 1px solid #999999;
					border-radius: 27rpx;
					font-size: 26rpx;
					color: #666;
					height: 54rpx;
					padding: 0 32rpx;
				}
			}
		}
	}
</style>