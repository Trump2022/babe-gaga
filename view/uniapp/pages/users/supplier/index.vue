<template>
	<view class="supplier" :style="colorStyle">
		<view class="conters">
			<view class="header">
				<image :src="picUrl"></image>
				<view class="record acea-row row-center-wrapper" @click="record">申请记录<text class="iconfont icon-gengduo3"></text></view>
			</view>
			<form @submit="formSubmit">
				<view class="list">
					<view class='item acea-row row-between row-bottom'>
						<view>供应商名称</view>
						<input placeholder="请输入供应商名称" placeholder-class='placeholder' name='system_name' v-model="system_name" maxlength="20"></input>
					</view>
					<view class='item acea-row row-between row-bottom'>
						<view>联系人</view>
						<input placeholder="请输入联系人姓名" placeholder-class='placeholder' name='name' v-model="name" maxlength="10"></input>
					</view>
					<view class='item acea-row row-between row-bottom'>
						<view>联系电话</view>
						<input placeholder="请输入手机号" placeholder-class='placeholder' name='phone' v-model="phone"></input>
					</view>
					<view class='item acea-row row-between row-bottom'>
						<view>验证码</view>
						<view class="itemCon acea-row row-between row-bottom">
							<input class="code" placeholder="请输入验证码" placeholder-class='placeholder' name='captcha' v-model="captcha"></input>
							<button class="bnt acea-row row-center-wrapper" :class="disabled === true ? 'on' : ''" :disabled='disabled' @click="code">
								{{ text }}
							</button>
						</view>
					</view>
					<view class="tip">请上传营业执照及行业相关资质证明图片</view>
					<view class="info">(图片最多可上传10张,图片格式支持JPG、PNG、JPEG)</view>
					<view class="picList acea-row row-middle">
						<view class='pictrue' v-for="(item,index) in pics" :key="index" v-if="index<10">
							<image :src='item'></image>
							<text class='iconfont icon-guanbi1' @click='DelPic(index)'></text>
						</view>
						<view class='pictrue acea-row row-center-wrapper row-column' @click='uploadpic'
							v-if="pics.length < 10">
							<text class='iconfont icon-dizhi-tianjia'></text>
						</view>
					</view>
					<view class="protocol">
						<checkbox-group @change='ChangeIsDefault'>
							<checkbox class="animated" :class="inAnimation?'headShake':''" @animationend='inAnimation=false'
								:checked="protocol ? true : false" />已阅读并同意 <text class="font-color" @click="getAgreement">《供应商协议》</text>
						</checkbox-group>
					</view>
					<button form-type="submit" class="confirmBnt bg-color">提交申请</button>
				</view>
			</form>
		</view>
		<agreement ref="supplier" :showAgree='showAgree' @close='close'></agreement>
		<canvas canvas-id="canvas" v-if="canvasStatus"
			:style="{width: canvasWidth + 'px', height: canvasHeight + 'px',position: 'absolute',left:'-100000px',top:'-100000px'}"></canvas>
		<Verify @success="success" :captchaType="'blockPuzzle'" :imgSize="{ width: '330px', height: '155px' }"
			ref="verify"></Verify>
		<!-- #ifdef MP -->
		<!-- <authorize v-if="isShowAuth" @authColse="authColse" @onLoadFun="onLoadFun"></authorize> -->
		<!-- #endif -->
		<home v-if="navigation"></home>
	</view>
</template>

<script>
	import sendVerifyCode from "@/mixins/SendVerifyCode";
	import colors from '@/mixins/color.js';
	import Verify from '../components/verify/verify.vue';
	import agreement from '../components/agreement/index.vue';
	import home from '@/components/home';
	import { mapGetters } from "vuex";
	import { toLogin } from '@/libs/login.js';
	import { HTTP_REQUEST_URL } from '@/config/app';
	import { getCodeApi, registerVerify, applySupplier, userApply } from '@/api/user.js';
	export default{
		mixins: [sendVerifyCode,colors],
		components: {
			Verify,
			agreement,
			home
		},
		computed: mapGetters(['isLogin']),
		data(){
			return{
				id:0,
				protocol: false,
				keyCode: '',
				system_name:'',
				name:'',
				phone:'',
				pics: [],
				captcha:'',
				canvasWidth: "",
				canvasHeight: "",
				canvasStatus: false,
				inAnimation: false,
				isShowAuth: false,//是否隐藏授权
				protocol: false,
				imgHost:HTTP_REQUEST_URL,
				picUrl:'',
				showAgree:false
			}
		},
		onLoad(options){
			this.id = options.id || 0;
			if(this.id>0){
				this.supplierApply();
			}
			this.colorData();
			let imgHost = this.imgHost + '/statics/images/'
			let picList = [
				imgHost+'supplierApplyGreen.jpg',
				imgHost+'supplierApplyRed.jpg',
				imgHost+'supplierApplyBule.jpg',
				imgHost+'supplierApplyPink.jpg',
				imgHost+'supplierApplyOrange.jpg',
				imgHost+'supplierApplyGolden.jpg'
			]
			setTimeout(()=>{
				switch (this.colorNum) {
					case 1:
					  this.picUrl = picList[2]
						break;
					case 2:
					  this.picUrl = picList[0]
						break;
					case 3:
					  this.picUrl = picList[1]
						break;
					case 4:
					  this.picUrl = picList[3]
						break;
					case 5:
					  this.picUrl = picList[4]
						break;
					case 6:
					  this.picUrl = picList[5]
						break;	
					default:
					  this.picUrl = picList[2]
						break
				}
			},1)
		},
		onShow(){
			if (!this.isLogin) {
				toLogin();
			}
		},
		methods:{
			/**
			 * 授权回调
			 */
			onLoadFun(e) {
				this.isShowAuth = false;
			},
			// 授权关闭
			authColse(e) {
				this.isShowAuth = e
			},
			success(data) {
				this.$refs.verify.hide()
				getCodeApi()
					.then(res => {
						this.keyCode = res.data.key;
						this.getCode(data);
					})
					.catch(res => {
						this.$util.Tips({
							title: res
						});
					});
			},
			async getCode(data){
				let that = this;
				await registerVerify({
						phone: that.phone,
						type: 'supplier', 
						key: that.keyCode,
						captchaType: 'blockPuzzle',
						captchaVerification: data.captchaVerification,
					})
					.then(res => {
						that.$util.Tips({
							title: res.msg
						});
						that.sendCode();
					})
					.catch(res => {
						that.$util.Tips({
							title: res
						});
					});
			},
			formSubmit() {
				if (!this.protocol) {
					this.inAnimation = true
					return this.$util.Tips({
						title: '请先阅读并同意协议'
					});
				}
				if (!this.system_name) return this.$util.Tips({
					title: '请输入供应商名称'
				});
				if (!this.name) return this.$util.Tips({
					title: '请输入联系人姓名'
				});
				if (!this.phone) return this.$util.Tips({
					title: '请填写手机号码'
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(this.phone)) return this.$util.Tips({
					title: '请输入正确的手机号码'
				});
				if (!this.captcha) return this.$util.Tips({
					title: '请输入验证码'
				});
				applySupplier(this.id,{
					system_name: this.system_name,
					name: this.name,
					phone: this.phone,
					captcha: this.captcha,
					images: this.pics.slice(0,10)
				}).then(res => {
					uni.reLaunch({
						url: '/pages/users/supplier_state/index?id='+ res.data.id
					})
				}).catch(err => {
					return this.$util.Tips({
						title: err
					});
				});
			},
			ChangeIsDefault(e) {
				this.$set(this, 'protocol', !this.protocol);
			},
			supplierApply(){
				userApply(this.id).then(res=>{
					let data = res.data;
					this.system_name = data.system_name;
					this.name = data.name;
					this.phone = data.phone;
					this.pics = data.images;
				}).catch(err=>{
					return this.$util.Tips({
						title: err
					});
				})
			},
			record(){
				uni.navigateTo({
					url: '/pages/users/record_list/index'
				})
			},
			/**
			 * 删除图片
			 * 
			 */
			DelPic(index) {
				let that = this,
					pic = this.pics[index];
				let pics = that.pics.slice(0,10);
				pics.splice(index, 1);
				that.$set(that, 'pics', pics);
			},
			/**
			 * 上传文件
			 * 
			 */
			uploadpic() {
				let that = this;
				this.canvasStatus = true
				that.$util.uploadImageChange({count:8,url:'upload/image'}, function(res) {
					that.pics.push(res.data.url);
				}, (res) => {
					this.canvasStatus = false
				}, (res) => {
					this.canvasWidth = res.w
					this.canvasHeight = res.h
				});
			},
			/**
			 * 发送验证码
			 * 
			 */
			code(data) {
				let that = this;
				if (!that.protocol) {
					this.inAnimation = true
					return that.$util.Tips({
						title: '请先阅读并同意协议'
					});
				}
				if (!that.phone) return that.$util.Tips({
					title: '请填写手机号码'
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.phone)) return that.$util.Tips({
					title: '请输入正确的手机号码'
				});
				this.$refs.verify.show()
			},
			getAgreement(){
			   this.$refs.supplier.getAgreement();
			   this.showAgree = true;
			},
			close(){
			   this.showAgree = false;
			}
		}
	}
</script>

<style lang="scss">
	.supplier{
		height: 100vh; 
		background-color: var(--view-theme);
		padding-bottom: 10rpx;
		.conters{
			background-color: var(--view-theme);
			padding-bottom: 10rpx;
		}
		.header{
			width: 750rpx;
			height: 400rpx;
			position: relative;
			image{
				width: 100%;
				height: 100%;
			}
			.record{
				position: absolute;
				right: 0;
				top:0;
				width: 144rpx;
				height: 44rpx;
				color: #FFDDB8;
				background-color: rgba(0, 0, 0, 0.3);
				border-radius: 22rpx 0 0 22rpx;
				font-size: 24rpx;
				padding-left: 6rpx;
				margin-top: 22rpx;
				.iconfont{
					font-size: 18rpx;
					margin-left: 6rpx;
				}
			}
		}
		.list{
			width: 690rpx;
			background-color: #fff;
			margin: -108rpx auto 0 auto;
			border-radius: 24rpx;
			position: relative;
			z-index: 1;
			padding: 20rpx 51rpx 72rpx 51rpx;
			.item{
				height: 104rpx;
				border-bottom: 1px solid #E9E9E9;
				padding-bottom: 22rpx;
				position: relative;
				input{
					width: 388rpx;
					font-size: 28rpx;
				}
				.placeholder{
					font-size: 28rpx;
					color: #ccc;
				}
				.itemCon{
					width: 388rpx;
				}
				.bnt{
					width: 180rpx;
					height: 54rpx;
					border-radius: 27rpx;
					color: var(--view-theme);
					border:1px solid var(--view-theme);
					font-size: 26rpx;
					position: absolute;
					right: 0;
					bottom: 16rpx;
					&.on{
						color: #666;
						border:unset;
					}
				}
				.code{
					width: 200rpx;
				}
			}
			.tip{
				font-size: 28rpx;
				color: #666;
				margin-top: 36rpx;
			}
			.info{
				font-size: 22rpx;
				color: #999;
				margin-top: 10rpx;
			}
			.picList{
				margin-top: 70rpx;
				.pictrue{
					width: 120rpx;
					height: 120rpx;
					margin: 0 0 35rpx 25rpx;
					position: relative;
					color: #D8D8D8;
					background-color: #F7F7F7;
					&:nth-of-type(4n-3){
						margin-left: 0;
					}
					image{
						width: 100%;
						height: 100%;
						border-radius: 8rpx;
					}
					.icon-guanbi1{
						font-size: 38rpx;
						position: absolute;
						top: -20rpx;
						right: -20rpx;
					}
					.icon-dizhi-tianjia{
						font-size: 38rpx;
					}
				}
				
			}
			.protocol {
				color: #999999;
				font-size: 24rpx;
				/deep/uni-checkbox .uni-checkbox-input{
					margin-top: -4rpx;
					width: 34rpx;
					height: 34rpx;
				}
			}
			.confirmBnt{
				font-size: 32rpx;
				width: 588rpx;
				height: 86rpx;
				border-radius: 43rpx;
				color: #fff;
				margin: 52rpx auto 0 auto;
				text-align: center;
				line-height: 86rpx;
			}
		}
	}
</style>