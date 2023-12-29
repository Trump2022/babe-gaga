<template>
	<!-- 商品分类 -->
	<view :style="colorStyle">
		<!-- 门店的两种样式布局 -->
		<storeCate1 :info="info" :isFooter="isFooter" :pageVisible="pageVisible" ref="refresh2">
		</storeCate1>
		<pageFooter @newDataStatus="newDataStatus"></pageFooter>
		<template v-if="popupVisible">
			<view class="white"></view>
			<view class="mask"></view>
			<view class="popup">
				<view class="body">
					<view class="title">请选择用餐人数</view>
					<view class="spinner">
						<button :class="{ disabled: number == 1 }" :disabled="number == 1" class="btn" hover-class="none" @tap="changeNumber(-1)">
							<text class="iconfont icon-shangpinshuliang-jian"></text>
						</button>
						<view class="passive">{{ number }}</view>
						<button class="btn" hover-class="none" @tap="changeNumber(1)">
							<text class="iconfont icon-shangpinshuliang-jia"></text>
						</button>
					</view>
				</view>
				<view class="foot">
					<button class="btn" hover-class="none" @tap="addTableCode(number)">提交</button>
				</view>
			</view>
		</template>
		<!-- <authorize v-if="isShowAuth" @authColse="authColse" @onLoadFun="onLoadFun"></authorize> -->
	</view>
</template>

<script>
	import {
		mapGetters
	} from 'vuex';
	import colors from "@/mixins/color";
	import storeCate1 from './cart.vue'
	import pageFooter from '@/components/pageFooter/index.vue'
	import {
		colorChange
	} from '@/api/api.js';
	import {
		getTableData,
		addTableCode,
		isTableCode,
		changeTable,
		getStoreData,
	} from '@/api/store.js';
	import {
	  toLogin
	} from '@/libs/login.js';
	import util from '../../../utils/util.js';
	export default {
		computed: mapGetters(['isLogin', 'uid']),
		components: {
			storeCate1,
			pageFooter
		},
		mixins: [colors],
		data() {
			return {
				info: {},
				id: 0,
				isFooter: false,
				category: '',
				customerType: 1,
				cart_num: 0,
				number: 1,
				popupVisible: false,
				pageVisible: true,
				isShowAuth: false,
			}
		},
		onLoad(options) {
			if (options.scene) {
				options = util.getUrlParams(decodeURIComponent(options.scene));
			}
			const { store_id, qrcode_id } = options;
			this.info = { ...this.info, store_id, qrcode_id };
		},
		onShow() {
			this.info.tableId = 0;
			if (this.isLogin) {
				this.getTableData();
			} else {
				toLogin()
			}
		},
		onHide() {
			this.pageVisible = false;
		},
		methods: {
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			// 授权回调
			onLoadFun() {
				this.isShowAuth = false;
				this.getTableData();
			},
			// 获取桌码配置
			getTableData() {
				getTableData({
					store_id: this.info.store_id
				}).then(res => {
					let data = res.data;
					if (Array.isArray(data)) {
						data = {};
					}
					const { store_code_switch, store_number_diners_window } = data;
					// 门店是否开启桌码
					if (store_code_switch == 1) {
						this.isTableCode(store_number_diners_window);
					} else{
						this.$util.Tips({
							title: '当前门店未开启桌码'
						});
					}
				});
			},
			// 选择用餐人数
			changeNumber(value) {
				this.number += value;
			},
			// 记录桌码
			addTableCode(number) {
				const { store_id, qrcode_id, tableId: y_tableId } = this.info;
				let data = { store_id, qrcode_id };
				if (number) {
					data.number = number;
				}
				return new Promise((resolve, reject) => {
					addTableCode(data).then(res => {
						this.info = { ...this.info, tableId: res.data.tableId };
						this.getStore();
						resolve(y_tableId);
						if (number) {
							this.popupVisible = false;
						}
					}).catch(err => {
						reject(y_tableId);
						this.$util.Tips({
							title: err
						});
					});
				});
			},
			// 检查桌码记录
			isTableCode(store_number_diners_window) {
				const { store_id, qrcode_id } = this.info;
				isTableCode({ store_id, qrcode_id }).then(res => {
					const { code, tableId } = res.data;
					this.info = { ...this.info, tableId };
					// 判断是不是换桌
					if (!code) {
						return uni.showModal({
							title: '确定要换桌吗？',
							content: '换桌后，您已点的商品会自动转移到新桌',
							success: (res) => {
								if (res.confirm) {
									this.changeTable();
								} else{
									this.getStore();
								}
							}
						});
					}
					// 判断这桌是否记录桌码
					if (tableId) {
						this.getStore();
					} else{
						// 是否弹出选择人数弹窗
						this.popupVisible = store_number_diners_window == 1;
						if (!this.popupVisible) {
							this.addTableCode();
						}
					}
				});
			},
			// 处理换桌商品
			async changeTable() {
				const y_tableId = await this.addTableCode();
				if (!y_tableId) {
					return;
				}
				changeTable({
					tableId: this.info.tableId,
					y_tableId
				}).then(() => {
					this.$util.Tips({
						title: '换桌完成',
						success: this.getStore
					});
				}).catch(err => {
					this.$util.Tips({
						title: err
					});
				});
			},
			// 获取接口附近门店
			getStore() {
				getStoreData({
					store_id: this.info.store_id
				}).then(res => {
					this.info = {
						...this.info,
						...res.data
					};
					this.$refs.refresh2.getMarTop();
					this.$refs.refresh2.getAllCategory();
				});
			},
			newDataStatus(val) {
				this.isFooter = val;
			},
		},
		onReachBottom: function() {
			setTimeout(() => {
				this.$refs.refresh2.getProducts()
			}, 10)
		}
	}
</script>
<style scoped lang="scss">
	/deep/.goodCate .uni-badge-left-margin .uni-badge--error{
		background-color: #fff !important;
		color: var(--view-theme);
		border-color: var(--view-theme);
		z-index: 8;
	}
	/deep/.goodCate .footer .cartIcon .uni-badge-left-margin .uni-badge--error{
		right: 0 !important;
		top: 10px !important;
	}
	/deep/.mask {
		z-index: 99;
	}

	/deep/.good-cate {
		padding: 80rpx;
	}

	.white {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		z-index: 100;
		background-color: #FFFFFF;
	}

	.mask {
		z-index: 100;
	}

	.popup {
		position: fixed;
		top: 50%;
		left: 50%;
		z-index: 100;
		width: 600rpx;
		border-radius: 16rpx;
		background-color: #FFFFFF;
		transform: translate(-50%, -50%);

		.body {
			padding: 52rpx 0;
		}

		.title {
			display: flex;
			justify-content: center;
			align-items: center;
			font-weight: 500;
			font-size: 34rpx;
			color: #282828;
		}

		.spinner {
			display: flex;
			justify-content: center;
			align-items: center;
			margin-top: 46rpx;

			.btn {
				background-color: transparent;
				.iconfont {
					display: inline-block;
					vertical-align: middle;
					text-align: center;
					line-height: 56rpx;
					width: 56rpx;
					height: 56rpx;
					border-radius: 28rpx;
					background-color: var(--view-theme);
					font-size: 22rpx;
					color: #FFFFFF;
				}

				&.disabled {
					.iconfont {
						background-color: #F5F5F5;
						color: #000000;
					}
				}
			}
		}

		.passive {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 98rpx;
			font-weight: 600;
			font-size: 36rpx;
			color: #000000;
		}

		.foot {
			display: flex;
			height: 98rpx;
			border-top: 1rpx solid #EEEEEE;

			.btn {
				flex: 1;
				display: flex;
				justify-content: center;
				align-items: center;
				font-size: 30rpx;
				color: var(--view-theme);
			}
		}
	}
</style>