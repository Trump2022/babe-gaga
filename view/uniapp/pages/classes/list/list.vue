<template>
	<view class="main">
		<view class='search acea-row row-between-wrapper'>
			<view class='input acea-row row-between-wrapper'>
				<text class='iconfont icon-sousuo2'></text>
				<input type='text' v-model="form.keyword" placeholder='搜索课程' placeholder-class='placeholder'
					confirm-type="search" @confirm="searchBut()"></input>
			</view>
			<view class='bnt' @tap='searchBut'>搜索</view>
		</view>

		<view class="cate" v-show="showCateSelected">
			<scroll-view scroll-x enable-flex :scroll-into-view="scrollIndex" class="cate-list">
				<view class="cate-item" v-for="(item, index) in storeCategoryList" :id="'scroll'+index" :key="item.id"
					:class="index == on ? 'active' : ''" @click="switchCate(item, index)">
					{{item.cate_name}}
				</view>
			</scroll-view>
		</view>

		<view class="content">
			<view class="nav">
				<view @click="set_where(1)" :class="{'activeColor':active == 1}">
					{{title}}
				</view>
				<view @click='set_where(2)' :class="{'activeColor':active == 2}">
					价格
					<image v-if="price==1" src="/static/images/up.png" alt="" class="nav-img"></image>
					<image v-else-if="price==2" src='/static/images/down.png' class="nav-img"></image>
					<image v-else src='/static/images/horn.png' class="nav-img"></image>
				</view>
				<view @click='set_where(3)' :class="{'activeColor':active == 3}">
					销量
					<image v-if="stock==1" src="/static/images/up.png" alt="" class="nav-img"></image>
					<image v-else-if="stock==2" src='/static/images/down.png' class="nav-img"></image>
					<image v-else src='/static/images/horn.png' class="nav-img"></image>
				</view>
			</view>
		</view>
		
		<scroll-view class="scroll-y" scroll-y @scrolltolower="scrolltolower" :style="{height: showCateSelected ? 'calc(100vh - 310rpx)' : 'calc(100vh - 130rpx)'}">
			<view class="item" v-for="item in productList" :key="item.id"  @click="goto(item.product_id)">
				<image :src="item.recommend_image" mode="widthFix"></image>
				<view class="store-name">
					{{item.store_name}}
				</view>
				<view class="bottom">
					<view class="price">
						<view class="salc-price">
							<text>￥</text>
							{{item.price}}
						</view>
						<text class="del">￥{{item.ot_price}}</text>
					</view>
					<text>{{item.sales}}人付款</text>
				</view>
			</view>
			
			<view class="loadTitle">
				{{loadTitle}}
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import {
		storeCategory,
		getProducts,
	} from '@/api/store.js';
import { title } from 'process';
	export default {
		data() {
			return {
				form: {
					keyword: '',
					page: 1,
					limit: 10,
					priceOrder: '',
					salesOrder: '',
					is_class: 1,
					cid: 0,
					sid: 0
				},
				storeCategoryList: [],
				on: 0,
				active: 1,
				loadend: false,
				loading: false,
				price: 0,
				stock: 0,
				productList: [],
				loadTitle: '加载更多',
				refresherTriggered: true,
				scrollIndex: 'scroll' + 0,
				showCateSelected: true,
				title: '综合排序'
			}
		},
		onLoad(opt) {
			this.form.cid = opt.id || opt.cid || 0
			this.form.sid = opt.sid || 0
			if(opt.title) {
				this.title = opt.title
				uni.setNavigationBarTitle({
					title: opt.title
				})
				this.showCateSelected = false
				this.getProducts()
			} else {
				this.getStoreCategory()
			}
		},
		methods: {
			switchCate(item, index) {
				this.on = index
				this.form.page = 1
				this.form.cid = item.id
				this.getProducts()
			},
			searchBut() {
				this.form.page = 1
				this.getProducts()
			},
			getStoreCategory() {
				storeCategory({
					is_class: 1
				}).then(res => {
					this.storeCategoryList = res.data
					if(this.storeCategoryList.length == 0) return
					if(this.form.cid > 0) {
						this.on = this.storeCategoryList.findIndex(item => item.id == this.form.cid)
						this.scrollIndex = 'scroll' + this.on
					} else {
						this.form.cid = this.storeCategoryList[0].id
					}
					this.getProducts()
				})
			},
			// 操作
			set_where: function(e) {
				switch (e) {
					case 1:
						this.active = 1
						this.form.priceOrder = ""
						this.form.salesOrder = ""
						this.price = 0
						break;
					case 2:
						if (this.price == 0) this.price = 1;
						else if (this.price == 1) this.price = 2;
						else if (this.price == 2) this.price = 0;
						if (this.price == 1) {
							this.form.priceOrder = "asc"
						} else if (this.price == 2) {
							this.form.priceOrder = "desc"
						} else {
							this.form.priceOrder = ""
						}
						this.active = 2
						this.stock = 0;

						break;
					case 3:
						if (this.stock == 0) this.stock = 1;
						else if (this.stock == 1) this.stock = 2;
						else if (this.stock == 2) this.stock = 0;

						if (this.stock == 1) {
							this.form.priceOrder = ""
							this.form.salesOrder = "asc"
						} else if (this.stock == 2) {
							this.form.priceOrder = ""
							this.form.salesOrder = "desc"
						} else {
							this.form.salesOrder = ""
						}
						this.active = 3
						this.price = 0
						break;
				}
				this.loadend = false;
				this.$set(this.form, 'page', 1);
				this.getProducts()
			},
			getProducts() {
				let that = this;
				if (that.loading) return;
				that.loading = true;
				that.loadTitle = '';
				getProducts(this.form).then(res => {
					let list = this.form.page == 1 ? [] : that.productList;
					list = list.concat(res.data)
					let loadend = res.data.length < that.form.limit;
					that.loadend = loadend;
					that.loading = false;
					that.loadTitle = loadend ? '已到底啦~~' : '加载更多';
					that.$set(that, 'productList', list);
					that.$set(that.form, 'page', that.form.page + 1);
					this.refresherTriggered = false
					console.log(this.refresherTriggered)
				}).catch(err => {
					that.loading = false;
					that.loadTitle = '加载更多'
					this.refresherTriggered = false
				});
			},
			scrolltolower() {
				if(this.loadend) return
				this.$set(this.form, 'page', this.form.page++);
				this.getProducts()
			},
			scrolltoupper() {
				this.loadend = false;
				this.$set(this.form, 'page', 1);
				this.getProducts()
			},
			goto(id) {
				uni.navigateTo({
					url: `/pages/class_details/index?id=${id}`
					// url: `/pages/goods_details/index?id=${id}`
				})
			},
		}
	}
</script>

<style lang="scss" scoped>
	.main .search {
		padding: 20rpx 0 20rpx 10rpx;
		background-color: #fff;
		border-top: #EBEBEB 1rpx solid;
		border-bottom: #EBEBEB 1rpx solid;
	}

	.main .search .input {
		width: 580rpx;
		background-color: #f7f7f7;
		border-radius: 33rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
		height: 66rpx;
	}

	.main .search .input input {
		width: 460rpx;
		font-size: 28rpx;
	}

	.main .search .input .placeholder {
		color: #bbb;
	}

	.main .search .input .iconfont {
		color: #000;
		font-size: 35rpx;
	}

	.main .search .bnt {
		width: 120rpx;
		text-align: center;
		height: 66rpx;
		line-height: 66rpx;
		font-size: 28rpx;
		color: #282828;
	}

	.cate {
		background-color: #fff;
		padding: 30rpx;
		.cate-list {
			display: flex;
			height: 60rpx;

			.cate-item {
				margin-right: 66rpx;
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

	.content {
		height: 100%;
		position: -webkit-sticky;
		position: sticky;
		top: var(--window-top);
		z-index: 99;

		// margin-bottom: 200rpx;
		.nav {
			padding: 30rpx 50rpx;
			display: flex;
			justify-content: space-between;
			font-size: 28rpx;
			font-weight: 500;
			color: #666666;
			background: #f5f5f5;

			.nav-img {
				width: 15rpx;
				height: 18rpx;
				margin-left: 10rpx;
			}

			.icon-shaixuan {
				font-size: 24rpx;
				margin-right: 10rpx;
			}
		}
	}

	.activeColor {
		color: #FB4095 !important;
	}
	
	.scroll-y {
		height: calc(100vh - 310rpx);
	}
	
	.item {
		padding: 24rpx;
		padding-bottom: 36rpx;
		border-radius: 24rpx;
		margin: 0 18rpx 24rpx;
		background-color: #fff;
		
		image {
			width: 100%;
			border-radius: 12rpx;
		}
		
		.store-name {
			font-size: 28rpx;
			color: #231815;
			font-weight: bold;
			text-overflow: ellipsis;
			overflow: hidden;
			white-space: nowrap;
			margin: 12rpx 0;
		}
		
		.bottom {
			display: flex;
			justify-content: space-between;
			align-items: flex-end;
			
			text {
				font-size: 22rpx;
				color: #97A1AB;
			}
			
			.price {
				font-weight: bold;
				display: flex;
				align-items: flex-end;
				color: #FB4095;
				
				text {
					color: #97A1AB;
				}
				
				.salc-price {
					margin-right: 12rpx;
					font-size: 46rpx;
					height: 52rpx;
					
					text {
						font-weight: normal;
						font-size: 20rpx;
						color: #FB4095;
					}
				}
				
				.del {
					text-decoration: line-through;
				}
			}
		}
	}
	
	.loadTitle {
		text-align: center;
		color: #97A1AB;
		font-size: 24rpx;
		margin: 30rpx 0;
	}
</style>