<!--
 * @Author: 默°
 * @Date: 2023-12-29 18:02:42
 * @LastEditTime: 2024-05-07 18:35:46
 * @LastEditors: 默°
 * @Description: 搬什么砖，摸鱼吧
 * 舒适圈那么舒服为什么要离开
-->
<template>
<!-- 发票设置 -->
    <from-submit :validate="ruleValidate" :url="url" :title="title" :rules="rules"/>
</template>

<script>
import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
import buildData from '@/pages/setting/shop/buildData';

export default {
  name: 'commonForm',
  components: { fromSubmit },
  mixins: [buildData],
  data() {
    return {
      ruleValidate: {},
      rules: [],
      url: '',
      title: '',
      type: ''
    };
  },
  props: {
    typeMole: {
      type: String,
      default: ''
    }
  },
  watch: {
    typeMole() {
      this.rules = [];
      this.getNewFormBuildRule();
    }
  }
};
</script>

<style scoped>
/deep/.input-build-card{
  min-height: 600px;
}
/deep/.ivu-tabs{
  min-height: 600px;
}
</style>
