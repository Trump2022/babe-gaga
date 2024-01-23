<!--
 * @Author: 默°
 * @Date: 2024-01-13 14:22:19
 * @LastEditTime: 2024-01-13 14:49:47
 * @LastEditors: 默°
 * @Description: 搬什么砖，摸鱼吧
 * 舒适圈那么舒服为什么要离开
-->
<template>
    <div>
        <FormItem :label="title" class="input-build" :class="getClassName()">
            <div class="input-error-wrapper">
                <CheckboxGroup :ref="'radio'+field" v-model="valueModel" @on-change="changeEvent('change',$event)" :vertical="vertical">
                    <Checkbox v-for="item in options" :label="item.value" :key="item.value" >{{item.label}}</Checkbox>
                </CheckboxGroup>
                <!-- 错误提醒 -->
                <div v-if="errorMessage" class="error-wrapper">{{ errorMessage }}</div>
            </div>
            <!-- 说明 -->
            <div v-if="info" class="info-wrapper">{{ info }}</div>
        </FormItem>
        <div v-for="item in control" :key="item.value">
            <template v-if="item.value === valueModel">
                <use-component :validate="validate" :errorsValidate="errorsValidate" @changeValue="changeValue" :rules="item.componentsModel"></use-component>
            </template>
        </div>
    </div>
</template>

<script>
    import build from "./build";
    import components from "./index";

    export default {
        name: "checkboxBuild",
        mixins:[build, components],
        mounted() {
            // this.valueModel = parseFloat(this.valueModel);
        },
        components: {
            useComponent:() => import('./useComponent'),
        },
        props: {
            control: {
                type: Array,
                default() {
                    return [];
                }
            },
            vertical: {
                type: Boolean,
                default: false
            },
        },
        methods:{
            changeValue(e) {
                this.$emit('changeValue',{field:e.field,value:e.value});
            },
        },
    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
