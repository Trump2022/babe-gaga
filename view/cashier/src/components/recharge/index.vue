<template>
    <Modal :value="visible" title="充值" width="768" class-name="recharge-modal" @on-cancel="clear">
      <div class="infoData" v-if="userInfo">
        <div class="pictrue">
          <img :src="userInfo.avatar">
        </div>
        <div class="info">
          <div class="name">{{ userInfo.nickname }}<span v-if="userInfo.vip_name">{{ userInfo.vip_name }}</span></div>
          <div class="attr">
            <div v-if="userInfo.phone" class="item phone">{{ userInfo.phone }}</div>
            <div class="item">余额<span class="num">{{ userInfo.now_money }}</span></div>
            <div class="item">积分<span class="num">{{ userInfo.integral }}</span></div>
            <div v-if="userInfo.is_money_level" class="item">付费会员到期：<span class="time">{{ userInfo.is_ever_level ? '永久会员' : userInfo.overdue_time || '已过期' }}</span></div>
          </div>
        </div>
      </div>
      <Form :label-width="90">
        <FormItem label="充值方式：">
          <RadioGroup v-model="currentTab" type="button" button-style="solid" @on-change="changeTabs">
            <Radio :label="0">充值套餐</Radio>
            <Radio :label="1">自定义充值</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem v-if="currentTab == 0">
          <RadioGroup v-model="activeId" class="radio-border-group">
            <Radio v-for="item in moneyList" :key="item.id" :label="item.id" border>
              <div class="money">￥<span class="num">{{ item.price }}</span></div>
              <div>额外赠送：￥ {{ item.give_money }}</div>
            </Radio>
          </RadioGroup>
        </FormItem>
        <FormItem v-if="currentTab == 1">
          <InputNumber v-model="payPrice" :min="1" :max="9999999" placeholder="0.00"></InputNumber>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" size="large" long @click="save">提交</Button>
      </div>
    </Modal>
    <!-- <Modal v-model="modalPay" footer-hide width='430px' class="modalPay" @on-cancel='yuePayClear'>
      <div class="payPage">
        <div class="header acea-row row-center-wrapper">
          <div class="pictrue"><img src="../../assets/images/gold.png"></div>
          <div class="text">应收金额(元)</div>
        </div>
        <div class="money">¥<span class="num">{{ rechargeData.price }}</span></div>
        <Input v-model="payNum" ref="rechargeNum" size="large" type="url" @input="inputSaoMa"
               placeholder="请点击输入框聚焦扫码或输入编码号"
               style="margin-top: 16px;"/>
        <div class="process">
          <div class="pictrue">
            <img src="../../assets/images/process1.png">
          </div>
          <div class="list acea-row row-between-wrapper">
            <div class="item one">
              <div class="name">出示付款码</div>
              <div>支付宝/微信</div>
            </div>
            <div class="item two">
              <div class="name">扫描付款码</div>
              <div>扫码枪</div>
            </div>
            <div class="item three">
              <div class="name">确认收款</div>
              <div>收银台点击确认</div>
            </div>
          </div>
        </div>
      </div>
    </Modal> -->
</template>
<script>
import {userRechargelApi, userSaveApi, checkOrderApi} from '@/api/user';

export default {
  name: 'recharge',
  model: {
    prop: 'visible',
    event: 'close'
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    userInfo: {
      type: Object,
      default: () => {
      }
    }
  },
  data() {
    return {
      modal: false,
      timer: null,
      currentTab: 0,
      moneyList: [],
      active: 0,
      activeId: 0,
      modalPay: false,
      payNum: '',
      payPrice: 1,
      rechargeData: {
        uid: 0,
        price: '',
        rechar_id: 0,
        pay_type: 3,
        auth_code: ''
      },
      givePrice: 0,
      totalPrice: 0
    }
  },
  watch: {
    modal(n) {
      if (!n) {
        return
      }
      let that = this;
      document.onkeydown = function (e) {
        if (e.which == 13) {
          if (that.payNum) {
            that.rechargeData.auth_code = that.payNum;
            that.confirm();
          }
        }
      };
    }
  },
  created() {
    this.getList();
    let that = this;
    //扫码枪扫码，针对纯数字
    // document.onkeydown = function(e) {
    // 	if (e.which == 13) {
    // 		if (that.payNum) {
    // 			that.rechargeData.auth_code = that.payNum;
    // 			console.log('klklk');
    // 			that.confirm();
    // 		}
    // 	}
    // };
  },
  methods: {
    clear() {
      this.payPrice = 0;
      this.currentTab = 0;
      this.$emit('close', false);
    },
    yuePayClear() {
      this.$Message.destroy()
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null
      }
    },
    //扫码枪扫码，针对带有字母的
    inputSaoMa(e) {
      // setTimeout定时器的作用是，等待扫码枪输入完，拿到完整的二维码信息，再调接口（扫码枪输入速度大概8~20毫秒，手动输速度大概是80毫秒），否则拿不到完整的二维信息。
      // let val = e
      // if (val === '') return false
      // clearTimeout(this.endTimeout)
      // this.endTimeout = null
      // this.endTimeout = setTimeout(() => {
      // 	if (this.payNum === val) {
      // 		clearTimeout(this.endTimeout)
      // 		if (val) {
      // 			this.rechargeData.auth_code = val;
      // 			this.confirm();
      // 		}
      // 	}
      // }, 500)
    },
    changeTabs(e) {
      if (e == 0) {
        this.active = 0;
        if (this.moneyList.length) {
          this.activeId = this.moneyList[0].id; 
        }
      }
    },
    getList() {
      userRechargelApi().then(res => {
        this.moneyList = res.data.recharge_quota
        if (this.moneyList.length) {
          this.activeId = this.moneyList[0].id; 
        }
      })
    },
    activeMoney(index, item) {
      this.active = index;
    },
    save() {
      // this.modalPay = true;
      // this.$nextTick(() => {
      //   this.$refs.rechargeNum.focus();
      // })
      if (this.currentTab == 1) {
        this.rechargeData.rechar_id = 0;
        this.givePrice = 0;
        this.rechargeData.price = this.payPrice;
      } else {
        let result = this.moneyList.find(item => item.id == this.activeId);
        if (result) {
          this.rechargeData.price = result.price;
          this.rechargeData.rechar_id = result.id;
          this.givePrice = result.give_money;
        }
      }
      this.totalPrice = this.$computes.Add(this.rechargeData.price, this.givePrice);
      this.$emit('recharge', this.rechargeData);
    },
    confirm() {
      this.rechargeData.uid = this.userInfo.uid;
      userSaveApi(this.rechargeData).then(res => {
        this.payNum = '';
        let status = res.data.status;
        let orderId = res.data.data.order_id;
        switch (status) {
          case 'SUCCESS':
            this.$Message.success('支付成功');
            this.modalPay = false;
            this.modal = false;
            this.$emit("getSuccess", this.totalPrice);
            break;
          case 'PAY_ING':
            let msg = this.$Message.loading({
              content: '等待支付中...',
              duration: 0
            });
            this.checkOrderTime(orderId, msg);
            break;
          default:
            this.$Message.warning('支付失败');
            break;
        }
      }).catch(err => {
        this.payNum = '';
        this.$Message.error(err.msg)
      })
    },
    checkOrderTime(orderId, msg) {
      let that = this;
      let num = 1;
      let timer = this.timer = setInterval(function () {
        that.checkOrder(orderId, timer, msg);
        num++;
        if (num >= 60) {
          clearInterval(timer);
          msg();
          that.$Message.success("支付失败");
          // that.modalPay = false;
          // that.modal = false;
        }
      }, 1000)
    },
    checkOrder(orderId, timer, msg) {
      checkOrderApi(1, {order_id: orderId}).then(res => {
        if (res.data.status == true) {
          msg();
          this.$Message.success("支付成功");
          this.$emit("getSuccess", this.totalPrice);
          this.modalPay = false;
          this.modal = false;
          clearInterval(timer);
        }
      }).catch(err => {
        msg();
        this.$Message.error(err.msg)
      })
    }
  }
}
</script>

<style lang="stylus" scoped>
/deep/ .ivu-tabs-nav-scroll {
  padding 16px 24px 0 24px !important;
}

/deep/ .ivu-modal-footer {
  border-top: 0 !important;
}

/deep/ .ivu-input-number-large input {
  height 56px !important;
  line-height 56px !important;
}

/deep/ .ivu-input-number-large {
  height 56px !important;
}

/deep/ .ivu-input-number-large .ivu-input-number-input-wrap {
  height 56px !important;
}

/deep/ .ivu-input-number-large .ivu-input-number-handler {
  height 28px;
}

/deep/ .ivu-input-number-large .ivu-input-number-handler-up-inner {
  top 6px;
}

/deep/ .ivu-input-number-large .ivu-input-number-handler-down-inner {
  bottom 6px;
}

.payPage {
  text-align: center;
  padding: 16px;

  /deep/ .ivu-input {
    width: 394px !important;
    text-align: center;
  }

  .header {
    margin: 35px 0 3px 0;
  }

  .process {
    width 394px;
    height 158px;
    border: 1px dashed #D8D8D8;
    border-top: 1px dashed #fff;
    margin: -1px auto 0 auto;

    &.on {
      border-top: 1px dashed #D8D8D8;
      margin-top: 20px;

      .list {
        padding-left 14px !important
      }
    }

    .list {
      padding 6px 10px 0 3px;

      .item {
        font-size 12px;
        color #666;
        width: 24%;
        .name {
          color #333;
          font-size 13px;
          font-weight bold;
        }
      }
    }

    .pictrue {
      width 362px;
      height 68px;
      margin 24px auto 0 auto;

      img {
        width 100%;
        height 100%;
      }
    }
  }

  .pictrue {
    width: 18px;
    height: 18px;

    img {
      width: 100%;
      height: 100%;
    }
    margin-right 7px
  }

  .text {
    color: rgba(0, 0, 0, 0.45);
    font-size: 14px;
  }

  .money {
    font-size: 18px;
    color: rgba(0, 0, 0, 0.85);

    .num {
      font-size: 32px;
      margin-left: 5px;
    }
  }

  .tip {
    width: 310px;
    height: 26px;
    background: rgba(255, 126, 0, 0.1);
    border-radius: 13px;
    font-size: 13px;
    color: #FF7E00;
    margin: 10px auto 0 auto;

    .icon {
      font-size: 16px;
      margin-right: 5px;
    }
  }

  .bnt {
    width: 394px;
    height 38px;
    margin: 28px 0 15px 0;
  }
}

.button {
  width: 535px;
  height: 36px;
  background: #1890FF;
  margin-top 28px;
  margin-bottom 8px;
}

.discount {
  padding 0 24px;

  .custom {
    margin-top 24px;

    .inputNum {
      width: 100%;
    }

    .tip {
      font-size: 13px;
      color: rgba(153, 153, 153, 0.85);
      margin-top 15px;
    }
  }

  .infoData {
    .pictrue {
      width 32px;
      height 32px;
      border-radius 50%;

      img {
        width 100%;
        height 100%;
        border-radius 50%;
      }
    }

    .info {
      font-size: 14px;
      font-weight: 400;
      color: rgba(0, 0, 0, 0.85);

      span {
        padding: 0 8px;

        & ~ span {
          border-left: 1px solid #DDDDDD;
        }
      }
    }
  }

  .list {
    display flex
    flex-wrap wrap
    .item {
      width 165px;
      height 90px;
      border-radius: 7px;
      border: 1px solid #DADFE6;
      color: rgba(0, 0, 0, 0.5);
      font-size 12px;
      text-align center;
      padding 8px 0;
      margin-right 20px;
      margin-top 20px;
      cursor pointer;

      &:nth-child(3n) {
        margin-right 0;
      }

      .money {
        color: #0091FF;
        font-size 30px;
      }

      &:hover {
        background-color #0091FF;
        color #fff;

        .money {
          color #fff;
        }
      }

      &.on {
        background-color #0091FF;
        color #fff;

        .money {
          color #fff;
        }
      }
    }
  }
}
/deep/.recharge-modal {
  .ivu-modal-content {
    border-radius: 10px;
  }

  .ivu-modal-body {
    height: 475px;
    padding: 30px 25px;
    overflow-x: hidden;
  }

  .ivu-modal-body::-webkit-scrollbar {
    display: none;
  }

  .radio-border-group {
    margin: 0 -20px -20px 0;
  }

  .infoData {
    display: flex;
    align-items: center;
    margin-bottom: 30px;

    .pictrue {
      width: 60px;
      height: 60px;
    }

    img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
    }

    .info {
      flex: 1;
      margin-left: 14px;
    }

    .name {
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 18px;
      color: #303133;

      span {
        display: inline-block;
        padding: 2px 5px;
        border: 1px solid #1890FF;
        border-radius: 3px;
        margin-left 6px;
        font-weight: 400;
        font-size: 12px;
        line-height: 12px;
        color: #1890FF;
      }
    }

    .tag {
      padding: 2px 5px;
      border: 1px solid #1890FF;
      border-radius: 3px;
      margin-left 6px;
      vertical-align: middle;
      font-weight: 400;
      font-size: 12px;
      color: #1890FF;
    }

    .attr {
      display: flex;
      align-items: center;
      margin-top: 8px;
      font-size: 12px;
      color: #303133;
    }

    .item + .item {
      margin-left 20px;
    }

    .phone {
      font-size: 14px;
    }

    .num {
      margin-left 4px;
      font-weight: 600;
      font-size: 17px;
      color: #F5222D;
    }

    .time {
      font-size: 14px;
      color: #1890FF;
    }
  }

  .ivu-form-item:last-child {
    margin-bottom: 0;
  }

  .ivu-radio-border {
    display: inline-flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 200px;
    height: 110px;
    border-color: #1890FF;
    border-radius: 10px;
    margin: 0 20px 20px 0;
    box-shadow: 0 0 14px 0 rgba(0,84,161,0.18);
    font-size: 14px !important;
    color: #909399;

    .ivu-radio {
      display: none;
    }

    .money {
      font-weight: 500;
      font-size: 16px;
      color: #1890FF;
    }

    .num {
      font-size: 24px;
    }

    &.ivu-radio-wrapper-checked {
      background: #1890FF;
      color: #FFFFFF;

      .money {
        color: #FFFFFF;
      }
    }
  }

  .ivu-input-number {
    width: 460px;
    height: 36px;
  }

  .ivu-modal-footer {
    padding: 30px 25px;

    .ivu-btn {
      height: 50px;
      border-radius: 25px;
      background: #1890FF;
      font-size: 16px !important;
    }
  }
}
</style>
