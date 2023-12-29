<template>
  <Modal v-model="modals" scrollable closable title="商品规格" :mask-closable="false" width="634" class-name="attr-modal">
    <div class="productAttr">
      <div class="header">
        <div class="pictrue">
          <img :src="attr.productSelect.image"/>
        </div>
        <div class='text'>
          <div class="name line1">{{ attr.productSelect.store_name }}</div>
          <div class="info">库存 {{ attr.productSelect.stock }}</div>
          <div class="money">¥<span class="num">{{ attr.productSelect.price }}</span></div>
        </div>
      </div>
      <div class="attr">
        <div class="list" v-for="(item, indexw) in attr.productAttr" :key="indexw">
          <div class="title">{{ item.attr_name }}</div>
          <div class="listn acea-row">
            <div class="item acea-row row-center-wrapper"
                  :class="item.index === itemn.attr ? 'on' : ''"
                  v-for="(itemn, indexn) in item.attr_value" @click="tapAttr(indexw, indexn)"
                  :key="indexn">{{ itemn.attr }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div slot="footer">
      <Button type="primary" size="large" long :disabled="disabled" class="bnt" @click="goCat">{{isSkill?'立即购买': '确定' }}</Button>
    </div>
  </Modal>
</template>

<script>
export default {
  name: 'productAttr',
  props: {
    attr: {
      type: Object,
      default: () => {
      }
    },
    isCart: {
      type: Number,
      value: 0
    },
    disabled: {
      type: Boolean,
      value: false
    },
    isSkill: {
      type: Boolean,
      value: false
    }
  },
  data() {
    return {
      modals: false
    }
  },
  created() {
  },
  methods: {
    goCat: function () {
      this.$emit('goCat', this.isCart);
    },
    tapAttr: function (indexw, indexn) {
      let that = this;
      that.$emit("attrVal", {
        indexw: indexw,
        indexn: indexn
      });
      this.$set(this.attr.productAttr[indexw], 'index', this.attr.productAttr[indexw].attr_values[indexn]);
      let value = that
          .getCheckedValue()
          .join(",");
      that.$emit("ChangeAttr", value);
    },
    //获取被选中属性；
    getCheckedValue: function () {
      let productAttr = this.attr.productAttr;
      let value = [];
      for (let i = 0; i < productAttr.length; i++) {
        for (let j = 0; j < productAttr[i].attr_values.length; j++) {
          if (productAttr[i].index === productAttr[i].attr_values[j]) {
            value.push(productAttr[i].attr_values[j]);
          }
        }
      }
      return value;
    }
  }
}
</script>

<style lang="stylus" scoped>
::-webkit-scrollbar-thumb {
  -webkit-box-shadow: inset 0 0 6px #eee;
}

::-webkit-scrollbar {
  width: 4px !important;
  /*对垂直流动条有效*/
}

/deep/.attr-modal {
  .ivu-modal-content {
    border-radius: 10px;
  }

  .ivu-modal-header {
    padding: 14px 25px;
  }

  .ivu-modal-header-inner {
    font-size: 18px !important;
    color: rgba(0,0,0,0.85);
  }

  .ivu-modal-body {
    display: flex;
    height: 476px;
    padding: 20px 25px 0;
  }

  .ivu-modal-footer {
    padding: 17px 25px;
    border-top: 0;

    .bnt {
      height: 46px;
      border-color: #1890FF;
      border-radius: 23px;
      background: #1890FF;
      font-weight: 500;
      font-size: 16px !important;
    }
  }
}

.productAttr {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  .header {
    display: flex;
  }

  .pictrue {
    width: 116px;
    height: 116px;

    img {
      width: 100%;
      height: 100%;
      border-radius 10px;
    }
  }

  .text {
    flex: 1;
    display: flex;
    flex-direction column;
    min-width: 0;
    padding: 4px 0 4px 18px;
  }

  .attr {
    flex: 1;
    overflow-x: hidden;
  }

  .list {
    .title {
      color: rgba(0,0,0,0.85);
      font-size: 16px;
      font-weight: bold;
      line-height: 18px;
      padding: 20px 0;
    }

    .listn {
      margin: 0 -20px -16px 0;
      .item {
        line-height:18px;
        background: #F5F5F5;
        border-radius: 18px;
        color: #666666;
        font-size 14px;
        margin: 0 20px 16px 0;
        cursor: pointer;
        padding: 9px 14px;

        &.on {
          background: #1890FF;
          color: #fff;
        }
      }
    }
  }

  .name {
    color: rgba(0,0,0,0.85);
    font-size: 18px;
  }

  .info {
    flex: 1;
    font-size: 13px;
    color: #999999;
    margin: 12px 0 0;
  }

  .money {
    font-size: 18px;
    color: #F5222D;
    font-weight: bold;

    .num {
      font-size: 22px;
    }
  }
}
</style>
