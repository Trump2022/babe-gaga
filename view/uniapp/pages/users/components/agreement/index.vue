<template>
	<view v-if="showAgree">
		<view class="agreement">
			<view class="title">供应商入驻协议<text class="iconfont icon-guanbi1" @click="onclose"></text></view>
			<view class="conter">
				<jyf-parser :html="content" ref="article" :tag-style="tagStyle"></jyf-parser>
			</view>
		</view>
		<view class="mask"></view>
	</view>
</template>

<script>
	import parser from "@/components/jyf-parser/jyf-parser";
	import {
		getUserAgreement,
	} from '@/api/user.js';
	export default{
		components: {
			"jyf-parser": parser
		},
		props: {
			showAgree: {
				type: Boolean,
				default: true
			},
		},
		data(){
			return{
				tagStyle: {
					img: 'width:100%;display:block;',
					table: 'width:100%',
					video: 'width:100%'
				},
				content: '',
			}
		},
		mounted() {},
		methods:{
			getAgreement(){
				getUserAgreement('supplier').then(res => {
					this.content = res.data.content
				}).catch(err => {
					that.$util.Tips({
						title: err.msg
					});
				})
			},
			onclose(){
				this.$emit('close')
			}
		}
	}
</script>

<style lang="scss">
	.agreement{
		width: 656rpx;
		height: 916rpx;
		background-color: #fff;
		padding: 52rpx 52rpx 58rpx 52rpx;
		position: fixed;
		top:50%;
		left:50%;
		margin-left: -328rpx;
		margin-top: -458rpx;
		z-index: 10;
		border-radius: 24rpx;
		.title{
			color: #282828;
			text-align: center;
			font-size: 32rpx;
			margin-bottom: 42rpx;
			position: relative;
			.iconfont {
				color: #E8E8E8;
				position: absolute;
				font-size: 45rpx;
				top:-40rpx;
				right: -40rpx;
			}
		}
		.conter{
			width: 100%;
			height: 720rpx;
			overflow: auto;
		}
	}
</style>