<template>
	<!-- 富文本 -->
	<view v-show="!isSortType">
		<view class='richText' :style="'background-color:'+bgColor+';margin:'+ udConfig +'rpx '+ lrConfig +'rpx;'" v-if="description">
			<!-- #ifndef APP-PLUS -->
			<jyf-parser :html="description" ref="article" :tag-style="tagStyle"></jyf-parser>
			<!-- #endif -->
			<!-- #ifdef APP-PLUS -->
			<view v-html="description"></view>
			<!-- #endif -->
		</view>
	</view>
</template>


<script>
	import parser from "@/components/jyf-parser/jyf-parser";
	export default {
		name: 'richText',
		props: {
			dataConfig: {
				type: Object,
				default: () => {}
			},
			isSortType:{
				type: String | Number,
				default:0
			}
		},
		components: {
			"jyf-parser": parser
		},
		data() {
			return {
				tagStyle: {
					img: 'width:100%;display:block;',
					table: 'width:100%',
					video: 'width:100%'
				},
				bgColor:this.dataConfig.bgColor.color[0].item,
				lrConfig:this.dataConfig.lrConfig.val,
				description:this.dataConfig.richText.val,
				udConfig:this.dataConfig.udConfig.val
			};
		},
		created() {
			if(this.description){
				this.description = this.description.replace(
					/<img/gi,
					'<img style="max-width:100%;height:auto;float:left;display:block" '
				);
				this.description = this.description.replace(
					/<video/gi,
					'<video style="width:100%;height:auto;display:block" '
				);
			}
		},
		methods: {
			
		}
	}
</script>

<style lang="scss">
	.richText{
		padding: 20rpx;
		background-color: #fff;
		margin: 0 20rpx;
		border-radius: 24rpx;
	}
	/deep/uni-video{
		width: 100%!important;
	}
	/deep/video{
		width: 100%!important;
	}
</style>
