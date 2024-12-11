<template>
	<view class='search acea-row row-between-wrapper'>
		<view class='input acea-row row-between-wrapper'>
			<view class="menu-box dropdown" v-if="type == 0">
				<view class='menu' @click="showDropdown = !showDropdown">
					{{searchType || ''}}
					<view class="triangle"></view>
				</view>
				
				<div class="dropdown-content" v-if="showDropdown">
					<view v-for="(item, index) in dropdownList" :class="{ 'dropdown-item': true, 'dropdown-active': active == index }" @click="(active = index, showDropdown = false)">
						{{item.label}}
					</view>
				  </div>
			</view>
			<text class="iconfont icon-sousuo" v-else></text>
			<input :placeholder='`搜索名称`' placeholder-class='placeholder' confirm-type='search' name="search"
				v-model="keyword" @confirm="handleSearch"></input>
		</view>
		<text class="search-btn" @click="handleSearch">搜索</text>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'searchCom',
		props: {
			type: {
				type: Number,
				default: () => 0
			},
			value: {
				type: String,
				default: () => ''
			}
		},
		data() {
			return {
				active: 0,
				dropdownList: [],
				showDropdown: false,
				keyword: this.value
			}
		},
		computed: {
			searchType() {
				return this.dropdownList[this.active]?.label
			},
		},
		watch: {
			active(newVal) {
				this.getSelect()
				this.$emit('type', this.dropdownList[this.active]?.type || 1)
			}
		},
		async created() {
			if(this.type === 0) {
				this.getSelect()
			}
		},
		mounted() {
			this.keyword = this.value
		},
		methods: {
			async getSelect() {
				// let res = await getSearchSelect({type: this.dropdownList[this.active]?.type || 1})
				// this.dropdownList = res.data.list
				// this.$emit('hotList', res.data.hot_list)
			},
			handleSearch() {
				this.$emit('handleSearch', this.keyword)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.search {
		width: 100%;
		height: 86rpx;
		padding: 0 36rpx 10rpx;
		box-sizing: border-box;
		background-color: #fff;
	}
	
	.search .input {
		width: 580rpx;
		height: 60rpx;
		background-color: #f5f5f5;
		border-radius: 50rpx;
		padding: 0 20rpx;
		box-sizing: border-box;
		display: flex;
	}
	
	.search .input input {
		flex: 1;
		height: 100%;
		font-size: 26rpx;
	}
	
	.search .input .placeholder {
		color: #999;
	}
	
	.search .input .iconfont {
		font-size: 35rpx;
		color: #555;
	}
	
	.search .icon-pailie,
	.search .icon-tupianpailie {
		color: #fff;
		width: 62rpx;
		font-size: 40rpx;
		height: 86rpx;
		line-height: 86rpx;
	}
	
	.menu-box {
		display: inline-flex;
		align-items: center;
		padding-right: 20rpx;
		position: relative;
		margin-right: 20rpx;
		height: 100%;
	}
	
	.menu-box::before {
		content: '';
		display: block;
		position: absolute;
		top: 50%;
		right: 0;
		width: 1rpx;
		height: 26rpx;
		background-color: #999;
		margin-top: -13rpx;
	}
	
	.menu {
		width: 120rpx;
		height: 86rpx;
		font-size: 26rpx;
		color: #666;
		text-align: center;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	
	.triangle {
	  width: 0;
	  height: 0;
	  border-left: 5px solid transparent;
	  border-right: 5px solid transparent;
	  border-bottom: 12rpx solid #999;
	  transform: rotate(180deg);
	  display: inline-block;
	  margin-left: 10rpx;
	}
	
	.dropdown {
	  position: relative;
	}
	
	.dropdown-content {
	  // display: none;
	  position: absolute;
	  background-color: #fff;
	  border-radius: 5px;
	  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
	  padding: 10px;
	  top: calc(100% + 10px);
	  left: 0;
	}
	
	.dropdown-content::before {
	  content: "";
	  position: absolute;
	  top: -12px;
	  left: 50%;
	  transform: translateX(-50%);
	  border: 6px solid transparent;
	  border-bottom-color: #fff;
	}
	
	.dropdown:hover .dropdown-content {
	  display: block;
	}
	
	.dropdown-item {
		height: 50rpx;
		line-height: 50rpx;
		color: #333;
		width: 100rpx;
		text-align: center;
	}
	
	.dropdown-active {
		color: var(--view-theme);
	}
	
	.icon-sousuo {
		margin-right: 20rpx;
	}
</style>