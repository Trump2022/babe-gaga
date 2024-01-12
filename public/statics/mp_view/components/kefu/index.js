(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/kefu/index"],{"18e2":function(e,t,n){"use strict";n.r(t);var u=n("f1ef"),c=n("a618");for(var o in c)["default"].indexOf(o)<0&&function(e){n.d(t,e,(function(){return c[e]}))}(o);n("9d4c");var f=n("f0c5"),i=Object(f["a"])(c["default"],u["b"],u["c"],!1,null,"07d58e1b",null,!1,u["a"],void 0);t["default"]=i.exports},"9d4c":function(e,t,n){"use strict";var u=n("f805"),c=n.n(u);c.a},a618:function(e,t,n){"use strict";n.r(t);var u=n("f57c"),c=n.n(u);for(var o in u)["default"].indexOf(o)<0&&function(e){n.d(t,e,(function(){return u[e]}))}(o);t["default"]=c.a},f1ef:function(e,t,n){"use strict";n.d(t,"b",(function(){return u})),n.d(t,"c",(function(){return c})),n.d(t,"a",(function(){}));var u=function(){var e=this.$createElement;this._self._c},c=[]},f57c:function(e,t,n){"use strict";(function(e){var u=n("4ea4");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var c=u(n("5c3b")),o={props:{customerList:{type:Array,default:[]},customerType:{type:Number,default:1}},mixins:[c.default],data:function(){return{show:!1}},mounted:function(){},methods:{closeDiscount:function(){this.$emit("closeKefu")},callPhone:function(t){1==this.customerType?e.makePhoneCall({phoneNumber:t.customer_phone}):e.navigateTo({url:"/pages/store/service/index?id="+t.id})}}};t.default=o}).call(this,n("543d")["default"])},f805:function(e,t,n){}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/kefu/index-create-component',
    {
        'components/kefu/index-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("18e2"))
        })
    },
    [['components/kefu/index-create-component']]
]);
