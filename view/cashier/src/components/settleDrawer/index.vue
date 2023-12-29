<template>
  <Drawer
    ref="drawer"
    :value="visible"
    width="800"
    class-name="settle-drawer"
    @on-close="close"
    @on-visible-change="visibleChange"
  >
    <div slot="header">
      <div class="title">选择支付方式</div>
      <div class="btn-group">
        <ButtonGroup shape="circle">
          <template v-for="item in list">
            <Button
              v-if="item.status"
              :key="item.label"
              :type="item.value === type ? 'primary' : 'default'"
              @click="typeChange(item.value)"
              >{{ item.label }}</Button
            >
          </template>
        </ButtonGroup>
      </div>
    </div>
    <div class="drawer-body">
      <div class="receivable">
        <div class="title"><span class="inner">应收金额(元)</span></div>
        <div class="money">
          ￥<span class="number">{{ money }}</span>
        </div>
      </div>
      <div v-if="type === 'cash'" class="counter">
        <div class="received">{{ collection }}</div>
        <div class="balance">
          需找零(元)：<span class="money"
            >￥{{
              money > collection
                ? 0
                : this.$computes.Sub(collection, money || 0)
            }}</span
          >
        </div>
        <div class="keypad">
          <div class="left">
            <Button v-for="item in numList" :key="item" @click="numTap(item)">{{
              item
            }}</Button>
          </div>
          <div class="right">
            <Button @click="delNum"
              ><Icon type="ios-backspace-outline"
            /></Button>
            <Button @click="delNum(-1)">C</Button>
            <Button class="enter" @click="cashBnt">确认</Button>
          </div>
        </div>
      </div>
      <div v-else-if="!type || verify" class="payment-code">
        <Input
          ref="input"
          v-model.trim="payNum"
          placeholder="请点击输入框聚焦扫码或输入编码号"
          @on-enter="submit"
        ></Input>
        <div class="tips-wrap" :class="{ balance: type === 'yue' }"></div>
      </div>
    </div>
    <div class="drawer-footer">
      <Button @click="close">取消收款</Button>
      <Button v-if="type === 'yue' && !verify" type="primary" @click="submit"
        >扣除余额</Button
      >
    </div>
  </Drawer>
</template>

<script>
export default {
  model: {
    prop: 'visible',
    event: 'close',
  },
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
    money: {
      type: [Number, String],
      default: 0,
    },
    collection: {
      type: [Number, String],
      default: 0,
    },
    zIndex: {
      type: [Number, String],
      default: 9999,
    },
    type: {
      type: String,
      default: '',
    },
    verify: {
      type: Boolean,
      default: false,
    },
    list: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      numList: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '00', '.'],
      payNum: '',
    };
  },
  watch: {
    type(value) {
      this.$nextTick(() => {
        if (!value || (value === 'yue' && this.verify)) {
          this.$refs.input.focus();
        }
      });
    },
    zIndex(value) {
      let drawerEl = this.$refs.drawer.$el;
      drawerEl.querySelector('.ivu-drawer-mask').style.zIndex = value;
      drawerEl.querySelector('.ivu-drawer-wrap').style.zIndex = value;
    }
  },
  methods: {
    close() {
      this.$emit('close', false);
    },
    visibleChange(visible) {
      this.$nextTick(() => {
        if (visible && (!this.type || (this.type === 'yue' && this.verify))) {
          this.$refs.input.focus();
        }
      });
    },
    typeChange(type) {
      this.payNum = '';
      this.$emit('payPrice', type);
    },
    numTap(item) {
      this.$emit('numTap', item);
    },
    delNum(type) {
      this.$emit('delNum', type);
    },
    cashBnt() {
      this.$emit('cashBnt');
    },
    submit() {
      if (this.payNum || (this.type === 'yue' && !this.verify)) {
        this.$emit('cashBnt', this.payNum);
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
::-webkit-scrollbar {
  display: none;
}

/deep/.settle-drawer {
  .ivu-drawer-content {
    display: flex;
    flex-direction: column;
  }

  .ivu-drawer-header {
    padding: 14px 20px 0;
    border-bottom-color: #EEEEEE;

    .title {
      font-size: 16px;
      color: #303133;
    }

    .btn-group {
      margin: 30px 0 32px;
      text-align: center;
    }

    .ivu-btn {
      width: 164px;
      height: 48px;
      border-color: #DDDDDD;
      font-size: 15px !important;
      color: #606266;

      &:focus {
        box-shadow: none;
      }
    }

    .ivu-btn-primary {
      border-color: #1890FF;
      background-color: #1890FF;
      color: #FFFFFF;
    }
  }

  .ivu-drawer-body {
    flex: 1;
    position: static;
    display: flex;
    flex-direction: column;
    padding: 40px 75px 30px;
  }

  .drawer-body {
    flex: 1;
    overflow-x: hidden;
  }

  .receivable {
    text-align: center;
    font-size: 14px;
    line-height: 20px;
    color: #999999;

    .inner {
      display: inline-block;
      padding-left: 27px;
      background-image: url('../../assets/images/gold.png');
      background-repeat: no-repeat;
      background-position: left center;
      background-size: 19px;
    }

    .money {
      margin-top: 15px;
      font-weight: 600;
      font-size: 22px;
      line-height: 38px;
      color: #333333;

      .number {
        font-size: 38px;
      }
    }
  }

  .payment-code {
    padding: 0 20px;
    margin-top: 50px;

    .ivu-input {
      height: 58px;
      padding: 0 20px;
      border-radius: 8px;
      font-size: 15px !important;
    }

    .tips-wrap {
      height: 279px;
      border: 1px dashed #DDDDDD;
      border-top: none;
      border-radius: 4px;
      background-image: url('../../assets/images/alipay.png');
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;

      &.balance {
        background-image: url('../../assets/images/balance.png');
      }
    }

    .tips-item {
      flex: 1;
      padding-top: 127px;
      padding-bottom: 38px;
      background-image: url('../../assets/images/process1.png');
      background-repeat: no-repeat;
      background-position: 40px 25px;
      background-size: 440px;
      text-align: center;
      font-size: 12px;
      line-height: 14px;
      color: #909399;

      &:nth-child(2) {
        background-position: -135px 25px;
      }

      &:nth-child(3) {
        background-position: -310px 25px;
      }
    }

    .tips-title {
      margin-bottom: 6px;
      font-size: 13px;
      font-weight: 500;
      color: #303133;
    }
  }

  .counter {
    padding: 20px;
    border-radius: 20px;
    margin-top: 40px;
    background-color: #F3F9FF;

    .received {
      height: 58px;
      padding: 0 20px;
      border: 1px solid #EEEEEE;
      border-radius: 8px;
      background-color: #FFFFFF;
      font-size: 26px;
      line-height: 58px;
      color: #303133;
    }

    .balance {
      padding: 18px 0 18px 10px;
      font-size: 15px;
      color: #303133;

      .money {
        color: #1890FF;
      }
    }

    .keypad {
      display: grid;
      grid-template-columns: auto auto auto auto;
      grid-gap: 10px;

      .left {
        grid-column-end: span 3;
        display: grid;
        grid-template-columns: auto auto auto;
        grid-gap: 10px;
      }

      .right {
        display: grid;
        grid-template-columns: auto;
        grid-gap: 10px;
      }

      .ivu-btn {
        height: 62px;
        border: 0;
        border-radius: 8px;
        font-weight: 500;
        font-size: 28px !important;
        line-height: 62px;
        color: #1890FF;

        &:focus {
          box-shadow: none;
        }
      }

      .enter {
        grid-row-end: span 4;
        height: 134px;
        background-color: #1890FF;
        font-weight: 500;
        font-size: 22px !important;
        line-height: 134px;
        color: #FFFFFF;
      }
    }
  }

  .drawer-footer {
    display: flex;
    padding: 0 80px;
    text-align: center;

    .ivu-btn {
      flex: 1;
      height: 50px;
      border-color: #1890FF;
      border-radius: 25px;
      font-weight: 500;
      font-size: 18px !important;
      color: #1890FF;

      +.ivu-btn {
        margin-left: 30px;
      }
    }

    .ivu-btn-primary {
      background-color: #1890FF;
      margin-left: 25px;
      color: #FFFFFF;
    }
  }
}
</style>