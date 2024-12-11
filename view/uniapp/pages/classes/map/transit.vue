<template>
	<view class="">
		<map id="map" :longitude="steps.startLongitude" :latitude="steps.startLatitude" :polyline="polyline" show-location
			show-scale :markers="marker"></map>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				steps: JSON.parse(uni.getStorageSync('transitItem')),
				polyline: [],
				marker: []
			}
		},
		onLoad() {
			this.setMarker()
			let stepList = this.steps.steps
			for (let j = 0; j < stepList.length; j++) {
				let item = stepList[j]
				if (item.mode == 'WALKING') {
					this.setPolyline(JSON.parse(JSON.stringify(item.polyline)), '#3287F4', '#3287F4', true)
				}
				if (item.mode == 'TRANSIT') {
					if (item.lines[0].vehicle == 'SUBWAY') {
						this.setPolyline(JSON.parse(JSON.stringify(item.lines[0].polyline)), '#E69E4A',
							'#FF6800')
					} else {
						this.setPolyline(JSON.parse(JSON.stringify(item.lines[0].polyline)))
					}
			
					if (j > 0 && item.lines && stepList[j - 1].lines && item.lines[0].geton.title == stepList[
							j - 1].lines[0].getoff.title) {
						this.marker.push({
							iconPath: 'https://shop.yaxhealth.com/static/images/change_line.png',
							width: 24,
							height: 24,
							id: j + 2,
							latitude: item.lines[0].geton.location.lat,
							longitude: item.lines[0].geton.location.lng
						})
					} else {
						this.marker.push({
							iconPath: 'https://shop.yaxhealth.com/static/images/get_up.png',
							width: 24,
							height: 24,
							id: j + 2,
							latitude: item.lines[0].geton.location.lat,
							longitude: item.lines[0].geton.location.lng
						}, {
							iconPath: 'https://shop.yaxhealth.com/static/images/get_down.png',
							width: 24,
							height: 24,
							id: j + 3,
							latitude: item.lines[0].getoff.location.lat,
							longitude: item.lines[0].getoff.location.lng
						})
					}
				}
			}
			
			let MapContext = wx.createMapContext('map');
			MapContext.includePoints({
				// 缩放视野展示所有经纬度 [上,右,下,左]  这里四个值要一致
				padding: [40, 10, 20, 10],
				points: [{
					longitude: this.steps.startLongitude,
					latitude: this.steps.startLatitude,
				}, {
					longitude: this.steps.endLongitude,
					latitude: this.steps.endLatitude,
				}],
			});
		},
		methods: {
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
			setMarker() {
				this.marker = [{
					iconPath: 'https://shop.yaxhealth.com/static/images/address_start.png',
					width: 36,
					height: 36,
					id: 1,
					zIndex: 999999999,
					latitude: this.steps.startLatitude,
					longitude: this.steps.startLongitude,
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
					latitude: this.steps.endLatitude,
					longitude: this.steps.endLongitude,
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
		}
	}
</script>

<style scoped>
	map {
		width: 100vw;
		height: 100vh;
	}
</style>