<template>
    <div>
        <FormItem :label="title" class="input-build" :class="getClassName()">
            <div class="input-error-wrapper input-wrapper-wrap" style="width:630px;">
                <div v-for="(item, index) in valueModel" :key="index" style="width: 255px; margin: 0 10px 10px 0;">
                    <InputNumber :prefix="prefix" @on-blur="changeEvent('blur',$event)" @on-keyup="changeEvent('keyup',$event)" @on-focus="changeEvent('focus',$event)" @on-click="changeEvent('click',$event)" @on-change="changeEvent('change',$event)" v-model="valueModel[index]" :name="field" :placeholder="placeholder" :maxlength="maxlength !== 0 ? maxlength : null" :rows="rows" :disabled="disabled" :style="styleModel" :suffix="suffix" />
                    <Button @click="valueModel.splice(index, 1)" v-if="index > 0" icon="md-close"></Button>
                </div>
                
                <!--随机token-->
                <Button  @click="randTokenClick" type="primary">新增面额</Button>
                <!--错误提醒-->
                <div v-if="errorMessage && !copy" class="error-wrapper">{{errorMessage}}</div>
            </div>
            <!--说明-->
            <div v-if="info" class="info-wrapper">{{info}}
              <Poptip placement="bottom" trigger="hover" :width="exampleSize[field]" :transfer="true" v-if="exampleImage[field]" padding="8px">
                <a>查看示例</a>
                <div class="exampleImg" :class="exampleSize[field] == 364?'on':''" slot="content">
                  <img
                      :src="baseURL+exampleImage[field]"
                      alt=""
                  />
                </div>
              </Poptip>
            </div>
        </FormItem>
    </div>
</template>

<script>
    import build from "./build";
    import Setting from '@/setting';
    export default {
        name: "arrBuild",
        mixins:[build],
        props:{
            rows: {
                type: Number,
                default: 2
            },
            disabled: {
                type: Boolean,
                default: false
            },
            randToken: {
                type: Number,
                default: 0,
            },
            maxlength: {
                type: Number,
                default: 0,
            }
        },
        data(){
          return {
            baseURL: Setting.apiBaseURL.replace(/adminapi/, '')
          };
        },
        methods: {
            handleCopySuccess(){
                this.$Message.success('复制成功');
            },
            encodingAESKeyGen() {
                 let letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                 let token = '';
                 for(var i = 0; i < 43; i++) {
                    var j = parseInt(Math.random() * 61 + 1);
                    token += letters[j];
                 }
                 return token;
            },
            token() {
                let letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let token = '';
                for(var i = 0; i < 32; i++) {
                    var j = parseInt(Math.random() * 61 + 1);
                    token += letters[j];
                }
                return token;
            },
            randTokenClick() {
                this.valueModel.push(0)
            },
            changeToken(){
                if (this.randToken === 1) {
                    this.valueModel = this.token()
                    this.$emit('changeValue',{field: this.field, value: this.valueModel});
                } else if (this.randToken === 2) {
                    this.valueModel = this.encodingAESKeyGen()
                    this.$emit('changeValue',{field: this.field, value: this.valueModel});
                }
            }
        },
    }
</script>

<style scoped>
    @import url('./css/build.css');
    .ivu-btn-primary {
        margin-left: 10px;
    }
</style>
