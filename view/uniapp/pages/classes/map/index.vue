<template>
	<view class="container">
		<view class="top">
			<view class="always-point">
				<view class="starting-point">
					<input class="weui-input" placeholder="请输入起点" v-model="startAddress"
						@input="search($event, 'start')" @focus="focus('start')" />
				</view>
				<view class="end-point">
					<input class="weui-input" placeholder="请输入终点" v-model="endAddress" @input="search($event, 'end')"
						@focus="focus('end')" />
				</view>
				<view class="exchange">
					<image src="https://shop.yaxhealth.com/static/images/exchange.png" mode="widthFix"
						@click="exchangeAddress"></image>
				</view>
			</view>
			<view class="travel-mode">
				<view class="travel" v-for="(item, index) in travelMode" :key="index"
					:class="travelActive == index ? 'active' : ''" @click="switchTravel(index)">
					<image :src="item.icon" mode="widthFix" v-show="travelActive == index"></image>
					<text>{{item.name}}</text>
				</view>
			</view>
		</view>
		<view v-show="showMap">
			<map id="map" :longitude="startLongitude" :latitude="startLatitude" :polyline="polyline" show-location
				show-scale :markers="marker"></map>
			<view class="bottom">
				<view class="drive">
					<view class="mode" v-if="travelActive === 1">
						<view class="item" v-for="(item, index) in travelModeList" :key="index"
							:class="driveActive== index ? 'drive-active' : ''" @click="switchDrive(index)">
							<view class="tags">
								{{ tagsText(item.tags[0]) || '方案' + (index + 1) }}
							</view>
							<view class="duration">
								{{ time(item.duration) }}
							</view>
							<view class="distance">
								<text>{{kmUnit(item.distance)}}</text>
								<image
									:src="`https://shop.yaxhealth.com/static/images/traffic_light${driveActive== index ? '_active' : ''}.png`"
									mode="widthFix"></image>
								<text>{{item.traffic_light_count}}</text>
								<text>￥{{item.toll}}</text>
							</view>
						</view>
					</view>
					<view class="mode" v-if="[2,3].includes(travelActive)">
						<view class="mode-time">
							{{time(modeObj.duration)}} {{kmUnit(modeObj.distance)}}
						</view>
					</view>
					<view class="btn" @click="openMapAPP">
						开始导航
					</view>
				</view>
			</view>
		</view>

		<scroll-view v-show="showSearch" scroll-y class="scroll-Y" @scrolltolower="loadMore">
			<view class="address-item" v-for="item in addressList" :key="item.id" @click="selectAddress(item)">
				<image src="https://shop.yaxhealth.com/static/images/address_default.png" mode="widthFix"></image>
				<view class="address-info">
					<view class="address-title">
						{{item.title}}
					</view>
					<view class="address-tag">
						{{item.category.split(':').pop()}}
					</view>
					<view class="address-full">
						{{item.address}}
					</view>
				</view>
				<view class="distance">
					{{kmUnit(item._distance)}}
				</view>
			</view>
			<view class="load-more">
				{{loadMoreText}}
			</view>
		</scroll-view>

		<view class="transit" v-show="showSelectTransit">
			<view class="transit-search">
				<view class="search-left" @click="showDateChange">
					<view>
						{{date[curValue[0]].title}}
						<br v-if="!['今天', '明天'].includes(date[curValue[0]].title)" />
						{{curHour > 9 ? curHour : '0' + curHour}}:{{curMinute > 9 ? curMinute : '0' + curMinute}}
					</view>
					<text class="iconfont icon-xiangxia"></text>
				</view>
				<scroll-view scroll-x class="search-scroll-x">
					<view class="search-box">
						<view :class="searchActive == 0 ? 'active-color' : ''" @click="switchTransitMode(0)">
							综合推荐
						</view>
						<view :class="searchActive == 1 ? 'active-color' : ''" @click="switchTransitMode(1)">
							时间短
						</view>
						<view :class="searchActive == 2 ? 'active-color' : ''" @click="switchTransitMode(2)">
							少换乘
						</view>
						<view :class="searchActive == 3 ? 'active-color' : ''" @click="switchTransitMode(3)">
							地铁优先
						</view>
						<view :class="searchActive == 4 ? 'active-color' : ''" @click="switchTransitMode(4)">
							少步行
						</view>
						<view :class="searchActive == 5 ? 'active-color' : ''" @click="switchTransitMode(5)">
							只坐地铁
						</view>
						<view :class="searchActive == 6 ? 'active-color' : ''" @click="switchTransitMode(6)">
							不坐地铁
						</view>
					</view>
				</scroll-view>
				<view class="picker-date" v-if="showDate">
					<picker-view :indicator-style="indicatorStyle" :value="value" @change="bindChange"
						class="picker-view">
						<picker-view-column>
							<view class="item date" v-for="(item,index) in date" :key="index">{{item.title}}  {{item.weekday}}</view>
						</picker-view-column>
						<picker-view-column>
							<view class="item" v-for="(item,index) in hours" :key="index">{{item}}时</view>
						</picker-view-column>
						<picker-view-column>
							<view class="item" v-for="(item,index) in minutes" :key="index">{{item}}分</view>
						</picker-view-column>
					</picker-view>
					<view class="date-btn" @click="submitDate">
						确定
					</view>
				</view>
				
				<view class="picker-mask" v-if="showDate" @click="showDate = false">
					
				</view>
			</view>
			<scroll-view scroll-y class="scroll-transit-Y">
				<view class="transit-item" v-for="(item, index) in transitList" :key="index" @click="goto(item)">
					<view class="transit-item-top">
						<view class="transit-item-top-left">
							{{time(item.duration)}}
							<image src="https://shop.yaxhealth.com/static/images/walk1.png" mode="widthFix"></image>
							<text>{{kmUnit(item.walk)}}</text>
						</view>
						<view class="transit-item-top-tag" v-if="item.tags && item.tags.length > 0">
							{{tagsText(item.tags[0])}}
						</view>
					</view>
					<view class="transit-item-center">
						<view class="transit-item-center-steps" v-for="(items, indexs) in item.steps" :key="indexs">
							<view class="" v-if="items.mode == 'WALKING'">
								<image src="https://shop.yaxhealth.com/static/images/walk1.png" mode="widthFix"></image>
								{{kmUnit(items.distance)}}
							</view>
							<view class="" v-else-if="items.mode == 'TRANSIT'">
								<view class="transit-box"
									:class="items.lines[0].vehicle == 'SUBWAY' ? 'subway' : 'bus'">
									{{ items.lines[0].vehicle == 'SUBWAY' ? items.lines[0].title : items.lines.reduce((t, item) => {
									t += (',' + item.title)
									return t
								}, '').split(',').filter(item => item != '').join('/')}}
								</view>
								<!-- {{items.lines.reduce((t, item) => {
									t += (',' + item.title)
									return t
								}, '').split(',').filter(item => item != '')}} -->
							</view>
							<view class="triangle" v-if="indexs < item.steps.length - 1"></view>
						</view>
					</view>
					<view class="transit-item-bottom">
						{{item.site}}站
					</view>
				</view>
			</scroll-view>
		</view>

	</view>
</template>

<script>
	import {
		getIndexData
	} from '@/api/api'
	import {
		Debounce
	} from '@/utils/validate.js'
	export default {
		data() {
			const now = new Date()
			const hour = now.getHours()
			const minute = now.getMinutes()
			const hours = []
			const minutes = []
			for (let i = hour; i <= 23; i++) {
				hours.push(i)
			}
			for (let i = minute; i <= 59; i++) {
				minutes.push(i)
			}
			return {
				startAddress: '',
				endAddress: '',
				startLatitude: 0,
				startLongitude: 0,
				endLatitude: 0,
				endLongitude: 0,
				polyline: null,
				marker: [],
				travelMode: [{
					name: '公交地铁',
					icon: 'https://shop.yaxhealth.com/static/images/transit.png'
				}, {
					name: '驾车',
					icon: 'https://shop.yaxhealth.com/static/images/drive.png'
				}, {
					name: '骑行',
					icon: 'https://shop.yaxhealth.com/static/images/ride.png'
				}, {
					name: '步行',
					icon: 'https://shop.yaxhealth.com/static/images/walk.png'
				}],
				travelActive: 1,
				travelModeList: [],
				tengxun_map_key: '',
				driveActive: 0,
				modeObj: {},
				page_index: 1,
				page_size: 20,
				showMap: true,
				showSearch: false,
				showSelectTransit: false,
				addressList: [],
				longitude: 0,
				latitude: 0,
				curInput: '',
				loadMoreText: '加载更多',
				mapCtx: null, // 地图实例
				searchActive: 0,
				transitList: [],
				policyList: ['RECOMMEND', 'LEAST_TIME', 'LEAST_TRANSFER', 'SUBWAY_FIRST', 'LEAST_WALKING', 'ONLY_SUBWAY',
					'NO_SUBWAY'
				],
				showDate: false,


				title: 'picker-view',
				date: this.getDatesAndWeekdays(),
				hours,
				minutes,
				value: [0, 0, 0],
				visible: true,
				curValue: [0, 0, 0],
				curHour: hour,
				curMinute: minute,
				curDate: Math.floor(new Date().getTime() / 1000)
			}
		},
		async onLoad({ startAddress, endAddress, startLatitude, startLongitude, endLatitude, endLongitude }) {
			this.startAddress = startAddress
			this.endAddress = endAddress
			this.startLatitude = startLatitude * 1
			this.startLongitude = startLongitude * 1
			this.endLatitude = endLatitude * 1
			this.endLongitude = endLongitude * 1
			// await this.getIndex()
			// let plugin = requirePlugin('routePlan');
			// let referer = 'wxa3825fc3a380d0b4';   //调用插件的app的名称
			// let endPoint = JSON.stringify({  //终点
			//     'name': '番禺广场地铁站',
			//     'latitude': 22.94088,
			//     'longitude': 113.39106
			// });
			// console.log(this.tengxun_map_key)
			// wx.navigateTo({
			// 	url: 'plugin://routePlan/index?key=N7LBZ-TVLRU-QYVVF-BYME4-4OQOF-WYFMT'+ '&referer=' + referer + '&endPoint=' + endPoint
			// })
			this.mapCtx = wx.createMapContext('map')
			this.getLocation()
			await this.getIndex()
			this.setMarker()
			this.switchTravel(this.travelActive)
		},
		methods: {
			goto(item) {
				uni.setStorageSync('transitItem', JSON.stringify({ ...item, startLongitude: this.startLongitude, startLatitude: this.startLatitude, endLatitude: this.endLatitude, endLongitude: this.endLongitude }))
				uni.navigateTo({
					url: '/pages/accompanying_services/map/transit'
				})
			},
			findChangedIndices(array1, array2) {
			  const changedIndices = [];
			  for (let i = 0; i < Math.min(array1.length, array2.length); i++) {
			    if (array1[i] !== array2[i]) {
			      changedIndices.push(i);
			    }
			  }
			
			  return changedIndices;
			},

			showDateChange() {
				this.showDate = true
				this.value = this.curValue
			},
			bindChange(e) {
				let index = this.findChangedIndices(this.value, e.detail.value)
				const now = new Date()
				let hour = now.getHours()
				let minute = now.getMinutes()
				const hours = []
				const minutes = []
				if(index < 2) {
					if(e.detail.value[0] > 0) {
						minute = 0
						hour = 0
					} else {
						hour = now.getHours()
						minute = now.getMinutes()
						if(e.detail.value[1] > 0) {
							minute = 0
						}
					}
					for (let i = hour; i <= 23; i++) {
						hours.push(i)
					}
					for (let i = minute; i <= 59; i++) {
						minutes.push(i)
					}
				}
				this.hours = [...new Set(hours)]
				this.minutes = [...new Set(minutes)]
				this.value = e.detail.value
			},
			convertDateFormat(inputDateStr) {
			  const formattedDateStr = inputDateStr.replace(/年|月/g, '/').replace(/日/g, '');
			  return formattedDateStr;
			},
			submitDate() {
				this.curValue = this.value
				this.curHour = this.hours[this.curValue[1]]
				this.curMinute = this.minutes[this.curValue[2]]
				this.showDate = false
				let date = null
				this.curDate = Math.floor(new Date(this.convertDateFormat(this.date[this.curValue[0]].date + ' ' + (this.hours[this.curValue[1]] > 9 ? this.hours[this.curValue[1]] : '0' + this.hours[this.curValue[1]]) + ':' + (this.hours[this.curValue[2]] > 9 ? this.hours[this.curValue[2]] : '0' + this.hours[this.curValue[2]]) + ':00')).getTime() / 1000)
				this.switchTravel(this.travelActive)
			},
			getDatesAndWeekdays() {
			  const datesAndWeekdays = [];
			  const daysOfWeek = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
			
			  const today = new Date();
			  const oneMonthLater = new Date(today);
			  oneMonthLater.setDate(oneMonthLater.getDate() + 30);
			
			  while (today <= oneMonthLater) {
				  const year = today.getFullYear()
			    const month = today.getMonth() + 1;
			    const date = today.getDate();
			    const dayOfWeek = daysOfWeek[today.getDay()];
			    
			    if (date === new Date().getDate()) {
			      datesAndWeekdays.push({
			        date: year + '年' + month + '月' + date + '日',
					title: '今天',
			        weekday: dayOfWeek
			      });
			    } else if (date === new Date().getDate() + 1) {
			      datesAndWeekdays.push({
			         date: year + '年' + month + '月' + date + '日',
					 title: '明天',
			        weekday: dayOfWeek
			      });
			    } else {
			      datesAndWeekdays.push({
					   date: year + '年' + month + '月' + date + '日',
			        title: month + '月' + date + '日',
			        weekday: dayOfWeek
			      });
			    }
			
			    today.setDate(today.getDate() + 1);
			  }
			
			  return datesAndWeekdays;
			},

			switchTransitMode(i) {
				this.searchActive = i
				this.switchTravel(this.travelActive)
			},
			getLocation() {
				uni.getLocation({
					type: 'wgs84',
					success: (res) => {
						this.longitude = res.longitude
						this.latitude = res.latitude
					},
					fail: e => {
						wx.getSetting({
							success: (res) => {
								if (res.authSetting['scope.userLocation'] === false) {
									// 用户未授权定位权限，可以提示用户，并打开授权设置页面
									wx.showModal({
										title: '提示',
										content: '需要获取您的定位权限',
										success: (modalRes) => {
											if (modalRes.confirm) {
												wx.openSetting({
													success: (settingRes) => {
														// 用户在设置页面授权后的回调
														if (settingRes
															.authSetting[
																'scope.userLocation'
																] === true
															) {
															// 用户重新授权了定位权限，可以进行相应操作

														}
													},
													complete: e => {

													}
												});
											} else {

											}
										}
									});
								} else {
									// 用户已经授权定位权限，可以进行相应操作

								}
							}
						});
					}
				})
			},
			selectAddress(item) {
				this[`${this.curInput}Latitude`] = item.location.lat
				this[`${this.curInput}Longitude`] = item.location.lng
				this[`${this.curInput}Address`] = item.title
				this.showSearch = false
				this.showMap = true
				this.setMarker()
				this.switchTravel(this.travelActive)
			},
			focus(key) {
				this.page_index = 1
				this.curInput = key
				this.getAddressList(this[`${this.curInput}Address`])
				this.showMap = false
				this.showSearch = true
			},
			loadMore() {
				if (this.loadMoreText == '已全部加载') return false
				this.page_index++
				this.getAddressList(this[`${this.curInput}Address`])
			},
			search: Debounce(async function(e, key) {
				if (!e.detail.value) return false
				this.page_index = 1
				this.curInput = key
				this.getAddressList(e.detail.value)
			}),
			async getAddressList(keyword) {
				this.loadMoreText = '加载中'
				let res = await this.getRoute(
					`https://apis.map.qq.com/ws/place/v1/suggestion?key=${this.tengxun_map_key}&keyword=${keyword}&page_index=${this.page_index}&page_size=${this.page_size}&location=${this.latitude},${this.longitude}`
					)
				let list = this.page_index == 1 ? [] : this.addressList
				list = list.concat(res.data)
				this.addressList = list
				if (res.data.length < this.page_size) {
					this.loadMoreText = '已全部加载'
				} else {
					this.loadMoreText = '加载更多'
				}
			},
			setMarker() {
				this.marker = [{
					iconPath: 'https://shop.yaxhealth.com/static/images/address_start.png',
					width: 36,
					height: 36,
					id: 1,
					zIndex: 999999999,
					latitude: this.startLatitude,
					longitude: this.startLongitude,
					callout: {
						content: '起',
						color: '#fff',
						display: 'ALWAYS',
						textAlign: 'center',
						bgColor: '#36ab60',
						padding: 2,
						anchorY: 27
					}
				}, {
					iconPath: 'https://shop.yaxhealth.com/static/images/address_end.png',
					width: 36,
					height: 36,
					id: 0,
					zIndex: 999999999,
					latitude: this.endLatitude,
					longitude: this.endLongitude,
					callout: {
						content: '终',
						color: '#fff',
						display: 'ALWAYS',
						textAlign: 'center',
						bgColor: '#d81e06',
						padding: 2,
						anchorY: 27
					}
				}]
			},
			async getIndex() {
				let res = await getIndexData()
				this.tengxun_map_key = res.data.tengxun_map_key
			},
			async switchTravel(index) {
				this.travelActive = index
				let res = await this.getRoute(this.getUrl())
				if (index > 0) {
					this.showSelectTransit = false
					this.showMap = true
					this.showSearch = false
					if (index == 1) {
						this.travelModeList = res.routes
						this.setPolyline(JSON.parse(JSON.stringify(this.travelModeList[this.driveActive].polyline)))
					} else {
						this.modeObj = res.routes[0]
						this.setPolyline(JSON.parse(JSON.stringify(this.modeObj.polyline)))
					}
					
					let MapContext = wx.createMapContext('map');
					MapContext.includePoints({
						// 缩放视野展示所有经纬度 [上,右,下,左]  这里四个值要一致
						padding: [40, 10, 20, 10],
						points: [{
							longitude: this.startLongitude,
							latitude: this.startLatitude,
						}, {
							longitude: this.endLongitude,
							latitude: this.endLatitude,
						}],
					});
				} else {
					// for(let j = 0; j < res.routes.length; j++) {
					// 	let item = res.routes[j]
					// 	if(item.mode == 'TRANSIT') {
					// 		this.setPolyline(JSON.parse(JSON.stringify(item.lines[0].polyline)))
					// 	} else if(item.mode == 'WALKING') {
					// 		for(let i = 0; i < item.steps.length; i++) {
					// 			if(item.steps[i].polyline) {
					// 				this.setPolyline(JSON.parse(JSON.stringify(item.steps[i].polyline)))
					// 			}
					// 		}
					// 	}
					// }
					this.showSelectTransit = true
					this.showMap = false
					this.transitList = res.routes
					this.transitList.forEach(item => {
						item.walk = 0
						item.site = 0
						let stepList = item.steps.filter(items => {
							if (items.mode == 'WALKING') {
								if (items.duration > 0) {
									item.walk += items.distance * 1
									return items
								}
							} else {
								item.site += items.lines[0].station_count * 1
								return items
							}
						})
						item.steps = stepList
					})
					// let stepList = res.routes[4].steps.filter(item => {
					// 	if (item.mode == 'WALKING') {
					// 		if (item.duration > 0) {
					// 			return item
					// 		}
					// 	} else {
					// 		return item
					// 	}
					// })

					// for (let j = 0; j < stepList.length; j++) {
					// 	let item = stepList[j]
					// 	if (item.mode == 'WALKING') {
					// 		this.setPolyline(JSON.parse(JSON.stringify(item.polyline)), '#3287F4', '#3287F4', true)
					// 	}
					// 	if (item.mode == 'TRANSIT') {
					// 		if (item.lines[0].vehicle == 'SUBWAY') {
					// 			this.setPolyline(JSON.parse(JSON.stringify(item.lines[0].polyline)), '#E69E4A',
					// 				'#FF6800')
					// 		} else {
					// 			this.setPolyline(JSON.parse(JSON.stringify(item.lines[0].polyline)))
					// 		}

					// 		if (j > 0 && item.lines && stepList[j - 1].lines && item.lines[0].geton.title == stepList[
					// 				j - 1].lines[0].getoff.title) {
					// 			this.marker.push({
					// 				iconPath: 'https://shop.yaxhealth.com/static/images/change_line.png',
					// 				width: 24,
					// 				height: 24,
					// 				id: j + 2,
					// 				latitude: item.lines[0].geton.location.lat,
					// 				longitude: item.lines[0].geton.location.lng
					// 			})
					// 		} else {
					// 			this.marker.push({
					// 				iconPath: 'https://shop.yaxhealth.com/static/images/get_up.png',
					// 				width: 24,
					// 				height: 24,
					// 				id: j + 2,
					// 				latitude: item.lines[0].geton.location.lat,
					// 				longitude: item.lines[0].geton.location.lng
					// 			}, {
					// 				iconPath: 'https://shop.yaxhealth.com/static/images/get_down.png',
					// 				width: 24,
					// 				height: 24,
					// 				id: j + 3,
					// 				latitude: item.lines[0].getoff.location.lat,
					// 				longitude: item.lines[0].getoff.location.lng
					// 			})
					// 		}
					// 	}
					// }
				}
			},
			exchangeAddress() {
				let {
					startAddress,
					endAddress,
					startLatitude,
					startLongitude,
					endLatitude,
					endLongitude
				} = this
				this.startAddress = endAddress
				this.endAddress = startAddress
				this.startLatitude = endLatitude
				this.endLatitude = startLatitude
				this.startLongitude = endLongitude
				this.endLongitude = startLongitude
				this.setMarker()
				this.switchTravel(this.travelActive)
			},
			switchDrive(index) {
				this.driveActive = index
				this.polyline = []
				this.$nextTick(() => {
					this.setPolyline(JSON.parse(JSON.stringify(this.travelModeList[this.driveActive].polyline)))
				})
			},
			getRoute(url) {
				return new Promise((resolve, reject) => {
					uni.request({
						url,
						success: res => {
							this.polyline = []
							resolve(res.data.result || res.data)
						}
					})
				})
			},
			getUrl() {
				let list = [
					`https://apis.map.qq.com/ws/direction/v1/transit/?from=${this.startLatitude},${this.startLongitude}&to=${this.endLatitude},${this.endLongitude}&key=${this.tengxun_map_key}&departure_time=${this.curDate}&policy=${this.policyList[this.searchActive]}`, // 公交
					`https://apis.map.qq.com/ws/direction/v1/driving/?key=${this.tengxun_map_key}&from=${this.startLatitude},${this.startLongitude}&to=${this.endLatitude},${this.endLongitude}&get_mp=1`, // 驾车
					`https://apis.map.qq.com/ws/direction/v1/bicycling/?from=${this.startLatitude},${this.startLongitude}&to=${this.endLatitude},${this.endLongitude}&key=${this.tengxun_map_key}`, // 骑行
					`https://apis.map.qq.com/ws/direction/v1/walking/?from=${this.startLatitude},${this.startLongitude}&to=${this.endLatitude},${this.endLongitude}&key=${this.tengxun_map_key}`, // 步行
				]
				return list[this.travelActive]
			},
			setPolyline(coors, color = '#0090FF', borderColor = '#E5F4FF', dottedLine = false) {
				let pl = []
				//坐标解压（返回的点串坐标，通过前向差分进行压缩）
				let kr = 1000000;
				for (let i = 2; i < coors.length; i++) {
					coors[i] = Number(coors[i - 2]) + Number(coors[i]) / kr;
				}
				//将解压后的坐标放入点串数组pl中
				for (let i = 0; i < coors.length; i += 2) {
					pl.push({
						latitude: coors[i],
						longitude: coors[i + 1]
					})
				}

				this.polyline.push({
					points: pl,
					color,
					width: 6,
					dottedLine,
					borderColor,
					borderWidth: 1
				})
			},
			openMapAPP() {
				this.mapCtx.openMapApp({
					longitude: this.endLongitude,
					latitude: this.endLatitude,
					destination: this.endAddress
				})
			},
			time(value) {
				const time = []
				const day = parseInt(value / 60 / 24)
				const hour = parseInt(value / 60 % 24)
				const min = parseInt(value % 60)
				time[0] = day > 0 ? day : 0
				time[1] = hour > 0 ? hour : 0
				time[2] = min > 0 ? min : 0
				return `${time[0] > 0 ? time[0] + '天' : ''}${time[1] > 0 ? time[1] + '小时' : ''}${time[2] > 0 ? time[2] + '分' : ''}`
			},
			kmUnit(m) {
				let v;
				if (typeof m === 'number' && !isNaN(m)) {
					if (m >= 1000) {
						v = (m / 1000).toFixed(2) + '公里'
					} else {
						v = m + '米'
					}
				} else {
					v = '0米'
				}
				return v;
			},
			tagsText(type) {
				let obj = {
					LEAST_LIGHT: '红绿灯少',
					EXPERIENCE: '经验路线',
					RECOMMEND: '推荐路线',
					LEAST_TIME: '时间最短',
					LEAST_DISTANCE: '距离最短',
					LEAST_TRANSFER: '少换乘',
					LEAST_WALKING: '少步行',
					LEAST_FEE: '少收费'
				}
				return obj[type] || type
			}
		}
	}
</script>

<style lang="scss" scoped>
	.transit {
		background-color: #F6F8FA;
		padding-top: 1rpx;

		.transit-search {
			height: 80rpx;
			display: flex;
			background-color: #fff;
			margin: 10rpx 0 20rpx;
			position: relative;
			z-index: 100;

			.search-left {
				width: 280rpx;
				display: flex;
				align-items: center;
				justify-content: center;

				.iconfont {
					font-size: 26rpx;
					margin-left: 20rpx;
				}
			}
			
			.picker-date {
				position: absolute;
				z-index: 99;
				bottom: -600rpx;
				left: 0;
				height: 600rpx;
				width: 100vw;
				background-color: #fff;
				
				.picker-view {
					height: 500rpx;
					.item {
						text-align: center;
						line-height: 34px;
					}
					
					.date {
						text-align: left;
					}
				}
				
				.date-btn {
					margin: 0 30rpx;
					height: 80rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					color: #fff;
					background-color: #538FFF;
					border-radius: 80rpx;
				}
			}
			
			.picker-mask {
				position: absolute;
				top: 100%;
				width: 100vw;
				height: calc(100vh - 296rpx - 94rpx);
				background-color: rgba(0, 0, 0, .3);
				z-index: 98;
			}
		}

		.search-scroll-x {
			width: calc(100vw - 280rpx);

			.search-box {
				display: flex;
				align-items: center;

				view {
					white-space: nowrap;
					display: inline-block;
					padding: 0 30rpx;
					line-height: 80rpx;
					color: #999999;
				}

				.active-color {
					color: #333;
				}
			}
		}

		.scroll-transit-Y {
			height: calc(100vh - 296rpx - 120rpx);
			padding-bottom: 40rpx;
			box-sizing: border-box;

			.transit-item {
				background-color: #fff;
				border-radius: 20rpx;
				padding: 30rpx;
				margin: 20rpx;

				&:first-child {
					margin-top: 0;
				}

				.transit-item-top {
					display: flex;
					justify-content: space-between;
					margin-bottom: 20rpx;

					.transit-item-top-left {
						font-size: 36rpx;
						font-weight: bold;

						image {
							width: 32rpx;
							margin: 0 6rpx 0 20rpx;
						}

						text {
							font-size: 26rpx;
						}
					}
					
					.transit-item-top-tag {
						padding: 4rpx 10rpx;
						background-color: #B2DDFF;
						color: #fff;
					}
				}

				.transit-item-center {
					display: flex;
					flex-wrap: wrap;

					.transit-item-center-steps {
						display: flex;
						align-items: center;
						margin-bottom: 20rpx;

						image {
							width: 26rpx;
						}

						.transit-box {
							padding: 6rpx 10rpx;
							border-radius: 6rpx;
							border: 2rpx solid transparent;
						}

						.subway {
							background-color: #0090FF;
							color: #fff;
							border-color: #0090FF;
						}

						.bus {
							border-color: #0090FF;
							background-color: #E5F4FF;
							color: #0090FF;
						}

						.triangle {
							width: 0;
							height: 0;
							border-left: 10rpx solid #868E98;
							border-top: 10rpx solid transparent;
							border-bottom: 10rpx solid transparent;
							margin: 0 10rpx;
						}
					}
				}
			}
		}
	}

	map,
	.map-box {
		width: 100vw;
		height: calc(100vh - 296rpx - 300rpx);
	}

	.scroll-Y {
		width: 100vw;
		height: calc(100vh - 296rpx);
		padding: 20rpx;
		box-sizing: border-box;

		.address-item {
			padding: 20rpx 30rpx;
			display: flex;
			align-items: center;

			image {
				width: 48rpx;
				margin-right: 10rpx;
			}

			.address-info {
				flex: 1;

				.address-title {
					font-size: 28rpx;
					font-weight: bold;
				}

				.address-tag {
					margin: 10rpx 0;
					display: inline-block;
					padding: 4rpx 8rpx;
					border: 1rpx solid #f5f5f5;
					font-size: 22rpx;
				}

				.address-full {
					font-size: 24rpx;
					color: #666;
				}
			}
		}

		.load-more {
			padding: 20rpx;
			text-align: center;
			color: #999;
		}
	}

	.top {
		background-color: #fff;
		height: 296rpx;
		padding: 20rpx 40rpx;
	}

	.always-point {
		background-color: #F7F7F7;
		border-radius: 20rpx;
		width: 100%;
		height: 176rpx;
		display: flex;
		flex-direction: column;
		position: relative;
	}

	.always-point view {
		flex: 1;
	}

	.always-point input {
		height: 100%;
		display: flex;
		align-items: center;
	}

	.starting-point {
		margin-left: 100rpx;
		position: relative;
		width: calc(100% - 200rpx);
		border-bottom: 2rpx solid #E5E5E5;

		&::before {
			content: '';
			display: block;
			position: absolute;
			left: -50rpx;
			top: 50%;
			margin-top: -7rpx;
			width: 14rpx;
			height: 14rpx;
			border-radius: 50%;
			background-color: #28C7A7;
			z-index: 9;
		}
	}

	.end-point {
		margin-left: 100rpx;
		position: relative;
		width: calc(100% - 200rpx);

		&::before {
			content: '';
			display: block;
			position: absolute;
			left: -50rpx;
			top: 50%;
			margin-top: -7rpx;
			width: 14rpx;
			height: 14rpx;
			border-radius: 50%;
			background-color: #FF5252;
			z-index: 9;
		}
	}

	.exchange {
		position: absolute;
		right: 0;
		height: 100%;
		width: 100rpx;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.exchange image {
		width: 50rpx;
	}

	.travel-mode {
		height: 120rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.travel {
		color: #222;
		display: flex;
		align-items: center;

		image {
			height: 46rpx;
			width: 46rpx;
			margin-right: 10rpx;
		}
	}

	.active {
		background-color: #E5F4FF;
		color: #0090FF;
		border-radius: 60rpx;
		padding: 6rpx 22rpx;
	}

	.bottom {
		height: 300rpx;

		.drive {
			.mode {
				padding: 20rpx 0;
				display: flex;
				align-items: center;
				height: 180rpx;
				flex-wrap: wrap;

				.item {
					flex: 1;

					view {
						text-align: center;
					}

					.tags {
						font-size: 24rpx;
					}

					.duration {
						font-size: 44rpx;
						margin: 6rpx 0;
					}

					.distance {
						font-size: 22rpx;

						image {
							width: 30rpx;
						}
					}
				}

				.drive-active {
					color: #0090FF;
				}

				.mode-time {
					font-size: 44rpx;
					font-weight: bold;
					width: 100%;
					padding-left: 30rpx;
					box-sizing: border-box;
				}

				.mode-steps {
					padding-left: 30rpx;
				}
			}

			.btn {
				margin: 0 30rpx;
				height: 80rpx;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
				background-color: #538FFF;
				border-radius: 80rpx;
			}
		}
	}
</style>