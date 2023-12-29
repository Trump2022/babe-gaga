<template>
	<view class="supplierState" :style="colorStyle">
		<view class="pictrue">
			<image v-if="type==0" :src="imgHost+'/statics/images/supplierApplyIng.png'"></image>
			<image v-else-if="type==1" :src="imgHost+'/statics/images/supplierApplySuccess.png'"></image>
			<image v-else :src="imgHost+'/statics/images/supplierApplyFail.png'"></image>
		</view>
		<view class="title">{{type==1?'恭喜，您的申请已通过！':type==2?'您的申请未通过！':'审核中'}}</view>
		<view class="info" v-if="type==0">您的申请正在审核中，请耐心等待！</view>
		<view class="info" v-else-if="type==2">请按提示修改您所填写的信息</view>
		<view class="webInfor" v-else>
			<view>后台网址：{{url}}</view>
			<view class="account">后台账号：{{account}}</view>
			<view>登录密码：{{pwd}}</view>
			<view>
				<!-- #ifndef H5 -->
				<text class='copy' @tap='copyWb'>复制</text>
				<!-- #endif -->
				<!-- #ifdef H5 -->
				<text class='copy copy-data' :data-clipboard-text="'网址:'+url+'\n账号:'+account+'\n密码:'+pwd">复制</text>
				<!-- #endif -->
			</view>
		</view>
		<button class="bnt bg-color acea-row row-center-wrapper" v-if="type==0 || type==2" @click="edit">{{type==0?'修改信息':'重新填写'}}</button>
		<button class="bnt on acea-row row-center-wrapper" :class="type==1?'ons':''" @click="back">返回上一页</button>
	</view>
</template>

<script>
	import ClipboardJS from "@/plugin/clipboard/clipboard.js";
	import { HTTP_REQUEST_URL } from '@/config/app';
	import colors from '@/mixins/color.js';
	import { userApply } from '@/api/user.js';
	export default{
		mixins: [colors],
		data(){
			return{
			   imgHost:HTTP_REQUEST_URL,
			   id:0,
			   type:0,
			   url:'',
			   account:'',
			   pwd:''
			}
		},
		onLoad(options){
			this.id = options.id;
			this.type = options.type || 0;
			this.supplierApply();
			// #ifdef H5
			this.$nextTick(function() {
				const clipboard = new ClipboardJS(".copy-data");
				clipboard.on("success", () => {
					this.$util.Tips({
						title: '复制成功'
					});
				});
			});
			// #endif
		},
		methods:{
			copyWb: function() {
				let that = this;
				uni.setClipboardData({
					data: '网址:'+this.url+'\n账号:'+this.account+'\n密码:'+this.pwd
				});
			},
			supplierApply(){
				userApply(this.id).then(res=>{
					let data = res.data;
					this.url = data.url;
					this.account = data.account;
					this.pwd = data.pwd;
				}).catch(err=>{
					return this.$util.Tips({
						title: err
					});
				})
			},
			edit(){
				uni.reLaunch({
					url: '/pages/users/supplier/index?id='+ this.id
				})
			},
			back(){
				if(this.type>0){
					uni.navigateBack({
						delta:1
					})
				}else{
					uni.reLaunch({
						url: '/pages/users/supplier/index?id=0'
					})
				}
			}
		}
	}
</script>

<style lang="scss">
	page{
		background-color: #fff;
	}
	.supplierState{
		text-align: center;
		padding-top: 142rpx;
		.pictrue{
			width: 340rpx;
			height: 280rpx;
			margin: 0 auto;
			image{
				width: 100%;
				height: 100%;
			}
		}
		.title{
			font-weight: 600;
			color: #282828;
			font-size: 34rpx;
		}
		.info{
			color: #999999;
			font-size: 26rpx;
			margin-top: 18rpx;
		}
		.webInfor{
			width: 660rpx;
			background: #F9F9F9;
			border-radius: 20rpx;
			padding: 32rpx 28rpx;
			font-size: 28rpx;
			color: #333;
			margin: 40rpx auto 0 auto;
			text-align: left;
			word-wrap: break-word;
			position: relative;
			.account{
				margin: 6rpx 0;
			}
			.copy{
				background-color: #999;
				border-radius: 60rpx;
				padding: 3rpx 15rpx;
				color: #fff;
				font-size: 20rpx;
				position: absolute;
				right: 28rpx;
				bottom: 28rpx;
			}
		}
		.bnt{
			width: 507rpx;
			height: 85rpx;
			border-radius: 43rpx;
			color: #fff;
			font-size: 30rpx;
			margin: 70rpx auto 0 auto;
			border:1px solid var( --view-theme );
			&.on{
				color: #333333;
				border: 1px solid #CCC;
				margin-top: 24rpx;
			}
			&.ons{
				margin-top: 64rpx;
			}
		}
	}
</style>