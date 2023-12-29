<template>
    <div class="c_radio mb15" v-if="configData">
        <div class="c_row-item">
            <Col class="c_label" :class="configData.type=='form'?'on':''" span="4">
                {{configData.title}}
            </Col>
            <Col class="color-box" span="19">
                <RadioGroup v-model="configData.tabVal" @on-change="radioChange($event)">
                    <Radio :label="key" v-for="(radio,key) in configData.tabList" :key="key">
                        <span>{{radio.name}}</span>
                    </Radio>
                </RadioGroup>
            </Col>
        </div>
    </div>

</template>

<script>
    export default {
        name: 'c_radio',
        props: {
            configObj: {
                type: Object
            },
            configNme: {
                type: String
            }
        },
        data () {
            return {
                defaults: {},
                configData: {}
            }
        },
        created () {
            this.defaults = this.configObj
            this.configData = this.configObj[this.configNme]
        },
        watch: {
            configObj: {
                handler (nVal, oVal) {
                    this.defaults = nVal
                    this.configData = nVal[this.configNme]
                },
                immediate: true,
                deep: true
            }
        },
        methods: {
            radioChange (e) {
                this.$emit('getConfig', e)
            }
        }
    }
</script>

<style scoped lang="less">
    .c_radio{
        .c_row-item{
           align-items: unset;
        }
        .c_label{
            color: #000;
            margin-right: 15px;
            margin-top: 4px;
            &.on{
              text-align: right;
              color: #666;
            }
        }
        /deep/.ivu-radio-wrapper{
          margin: 5px 25px 5px 0;
        }
        /deep/.ivu-radio{
            margin-right: 6px;
        }
    }
</style>
