<template>
  <view :style="colorStyle">
    <tui-drawer :mode="mode" :visible="visible" @close="closeDrawer">
			<!-- #ifdef MP  -->
			<view :style="'height:'+(statusBarHeight+43)+'px'"></view>
      <scroll-view scroll-y="true" class="scroll-Y" :style="'height: calc(100vh - '+(statusBarHeight+214)+'rpx)'">
			<!-- #endif -->
			<!-- #ifndef MP  -->
			<scroll-view scroll-y="true" class="scroll-Y" :style="'height: calc(100vh - 128rpx)'">
			<!-- #endif -->	
      <view class="d-container">
        <!-- 品牌 -->
        <view class="box">
          <view class="title">
            <view class="font">品牌</view>
            <view class="font-right" @click="openFn(1)" v-if="open==1">展开 <text
                class="iconfont icon-xialazhankai"></text>
            </view>
            <view class="font-right" v-if="open==2" @click="openFn(2)">收起 <text class="iconfont icon-xiangshang"></text>
            </view>
          </view>
          <!-- 品牌显示十个 -->
          <view class="box-list">
            <view class="list acea-row row-center-wrapper" :class="{'bgcolor':spanIndex.indexOf(index)>-1}" v-for="(item,index) in list"
              :key="index" @click="changeSpan(index,item)">
              {{item.brand_name}}
            </view>
          </view>

        </view>

        <!-- 分类 -->
        <view class="box">
          <view class="font">分类</view>
          <template v-for="(item,indexw) in storeArr">
            <tui-collapse :index="indexw" :current="item.current" :disabled="item.disabled" @click="change">
              <template v-slot:title>
                <view class="title" :key="item.id">
                  <view class="font-live">

                    <tui-list-cell :hover="!item.disabled">{{item.cate_name}}</tui-list-cell>

                  </view>

                </view>
              </template>
              <template v-slot:content>
                <view class="box-list">
                  <view class="list acea-row row-center-wrapper" v-for="(data,indexn) in item.children" :key="indexn"
                    @click="categoryFn(data,item)">
                    <view class="acea-row row-center-wrapper" :class="{bgcolor:sortIndex===data.id}">
                      {{data.cate_name}}
                    </view>

                  </view>
                </view>
              </template>
            </tui-collapse>
          </template>
        </view>
      </view>
      </scroll-view>
      <!-- 底部按钮 -->
      <view class="footer">
        <view class="btn" @click="submit(2)">
          重置
        </view>
        <view class="btn btnColor" @click="submit(1)">
          确认
        </view>
      </view>
    </tui-drawer>
    <!-- 确认 -->
  </view>
</template>

<script>
  import colors from "@/mixins/color";
  import tuiDrawer from "@/components/thorui/tui-drawer"
  import tuiCollapse from "@/components/thorui/tui-collapse"
  import tuiListCell from "@/components/thorui/tui-list-cell"
	let statusBarHeight = uni.getSystemInfoSync().statusBarHeight;

  export default {
    components: {
      tuiDrawer,
      tuiCollapse,
      tuiListCell
    },
    props: {
      storeCategory: {
        type: Array, // 分类数据
        default: []
      },
      storeBrand: {
        type: Array, //品牌数据
     
      }
    },
    mixins: [colors],
    data() {
      return {
				statusBarHeight:statusBarHeight,
        visible: false,
        mode: "right",
        sortIndex: 0,
        spanIndex: [],
        newList: [],
        open: 1,
        forArr: [],
        serchData: {
          sort: '', //new 最新, sales价值
          sort_type: '', // DESC 倒序 ASC 正序
          send: '',
          cate_id: [],
        }
      }
    },
    computed: {
      storeArr() {
        return this.storeCategory
      },
      list() {
        if(this.open===1) {
           return this.storeBrand.slice(0, 10)
        } else if( this.open===2) {
          return this.storeBrand
        }
      }
    },
    onLoad() {
    },
    mounted() {
          console.log(2,'父组件传过来的值')
          // this.newListArr()
      // this.openFn()
    },

    methods: {
      // newListArr() {
      //      // this.forArr = this.list
      //   if (this.open == 1) {
      //  console.log('进入')
      //     this.$set(this.forArr,this.forArr.length,...this.list.slice(0, 10))
      //      console.log(this.forArr)
        
      //   } else {
      //     console.log('全部')
      //     this.forArr = this.list
      //   }
      // },
      closeDrawer() {
        this.visible = false
      },
      // 点击展开
      openFn(num) {
        switch (num) {
          case 1:
            this.open = 2
       
            break;
          case 2:
            this.open = 1

            break;
        }
             // this.newListArr()
      },
      // 单选
      categoryFn(row,item) {
        console.log(row,item)
        this.sortIndex = row.id
        let data ={
          cid:item.id,
          sid:row.id
        }
        console.log(data)
        this.$emit('categoryChange',data)
      },
      change(e) {
        let index = e.index;
        let item = this.storeArr[index];
        item.current = item.current == index ? -1 : index

      },
      // 多选
      changeSpan(index, row) {
        let arrIndex = this.spanIndex.indexOf(index);
        if (arrIndex > -1) {
          this.spanIndex.splice(arrIndex, 1);
					this.newList.splice(arrIndex, 1);
        } else {
          this.spanIndex.push(index);
					this.newList.push(row.id);
        }
        let result = this.newList.join(",")
        this.$emit('brandChange', result)
      },
      // 确认提交
      submit(val) {
        if(val==2) {
           this.sortIndex =0
           this.spanIndex=[]
        }
         this.$emit('submitFn',val)
      }
    }
  }
</script>

<style lang="scss">
  .bgcolor {
    background: var(--view-minorColorT) !important;
    border: 1px solid var(--view-theme);
    border-radius: 34rpx;
    color: var(--view-theme) !important;
		height: 100%;
		width: 100%;
  }

  .font {
    margin-top: 30rpx;
    font-size: 28rpx;
    font-weight: 500;
    color: #333333;
  }

  .font-live {
    font-size: 24rpx;
    font-weight: 400;
    color: #666666;
  }

  .font-right {
    margin-top: 30rpx;
    font-size: 20rpx;
    font-weight: 400;
    color: #666666;
  }

  .icon-xialazhankai {
    font-size: 20rpx;
    color: #666666;
		margin-left: 6rpx;
  }

  .icon-xiangshang {
    font-size: 20rpx;
    color: #666666;
		margin-left: 6rpx;
  }

  .d-container {
    width:600rpx;
    padding: 0 34rpx 20rpx 34rpx;

    .box {
      .title {
				/* #ifndef MP */
				margin-top: 30rpx;
				/* #endif */
        display: flex;
        justify-content: space-between;
      }

      .box-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 20rpx;

        .list {
          width: 240rpx;
          height: 68rpx;
          background: #F5F5F5;
          border-radius: 34rpx;
          font-size: 24rpx;
          font-weight: 400;
          color: #666666;
          margin-top: 30rpx;
        }
      }
    }
  }

  .footer {
   
    // position: fixed;
    // bottom: 10rpx;
    margin: 20rpx 30rpx 38rpx 30rpx;
    display: flex;
    justify-content: space-between;

    .btn {
      width: 240rpx;
      height: 68rpx;
      background: #F5F5F5;
      border-radius: 34rpx;
      font-size: 24rpx;
      font-weight: 400;
      text-align: center;
      line-height: 68rpx;
    }

    .btnColor {
      background: var(--view-theme);
      color: #fff;
    }
  }
</style>
