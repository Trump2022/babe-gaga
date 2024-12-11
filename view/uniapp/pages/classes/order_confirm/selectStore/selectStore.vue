<template>
	<view class="box">
		<searchCom type="1" :value="searchData.keyword" @handleSearch="handleSearch"></searchCom>
		<scroll-view scroll-y show-scrollbar class="scroll-Y" refresher-enabled @refresherrefresh="refresherrefresh"  :refresher-triggered="triggered" @scrolltolower="scrolltolower">
			<view class="list">
				<view class="item" v-for="(item, index) in list" :key="index" @click="selectBack(item)">
					{{item.name || item.depart_name}}
				</view>
			</view>
			<loadMore :loadText="loadText"></loadMore>
		</scroll-view>
	</view>
</template>

<script>
	import searchCom from '../../components/searchCom'
	import loadMore from '../../components/loadMore'
	import {
		storeList
	} from '@/api/store.js';
	export default {
		components: {
			searchCom,
			loadMore
		},
		data() {
			return {
				loadText: '加载更多',
				list: [],
				searchData: {
					page: 1,
					keyword: '',
					limit: 20
				},
				triggered: false,
				acknowledgementOrder: {},
				type: 'hospital',
				id: 0
			}
		},
		onLoad(opt) {
			this.getList()
		},
		methods: {
			getList() {
				this.triggered = true
				storeList(this.searchData).then(res => {
					let list = this.searchData.page == 1 ? [] : this.list
					list = list.concat(res.data)
					this.list = list
					this.loadText = res.data.length < this.searchData.limit ? '已经到底了~' : '加载更多'
					this.triggered = false
				})
			},
			handleSearch(val) {
				this.searchData.keyword = val
				this.searchData.page = 1
				this.getList()
			},
			refresherrefresh() {
				this.searchData.page = 1
				this.getList()
			},
			scrolltolower() {
				if(this.loadText == '已经到底了~') return
				this.searchData.page++
				this.loadText = '正在加载中...'
				this.getList()
			},
			selectBack(item) {
				uni.$emit('store', item)
				uni.navigateBack()
			}
		}
	}
</script>

<style scoped lang="scss">
	.list {
		background-color: #fff;
		
		.item {
			height: 100rpx;
			line-height: 100rpx;
			color: #222;
			font-size: 30rpx;
			border-bottom: 1rpx solid #EBEBEB;
			padding-left: 36rpx;
			
			&:last-child {
				border-bottom: none;
			}
		}
	}
	
	.scroll-Y {
		height: calc(100vh - 86rpx);
	}
</style>