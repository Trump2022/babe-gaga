(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/goodList/index"],{"1a85":function(t,n,e){"use strict";var u=e("efe5"),a=e.n(u);a.a},"216a":function(t,n,e){"use strict";e.d(n,"b",(function(){return u})),e.d(n,"c",(function(){return a})),e.d(n,"a",(function(){}));var u=function(){var t=this.$createElement;this._self._c},a=[]},2609:function(t,n,e){"use strict";(function(t){Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var u=e("26cb"),a=e("3e4f"),i={computed:(0,u.mapGetters)(["uid"]),props:{status:{type:Number,default:0},bastList:{type:Array,default:function(){return[]}}},data:function(){return{}},methods:{goDetail:function(n){var e=this;(0,a.goPage)().then((function(u){(0,a.goShopDetail)(n,e.uid).then((function(e){t.navigateTo({url:"/pages/goods_details/index?id=".concat(n.id)})}))}))}}};n.default=i}).call(this,e("543d")["default"])},9572:function(t,n,e){"use strict";e.r(n);var u=e("216a"),a=e("dd51");for(var i in a)["default"].indexOf(i)<0&&function(t){e.d(n,t,(function(){return a[t]}))}(i);e("1a85");var o=e("f0c5"),c=Object(o["a"])(a["default"],u["b"],u["c"],!1,null,"15c8b520",null,!1,u["a"],void 0);n["default"]=c.exports},dd51:function(t,n,e){"use strict";e.r(n);var u=e("2609"),a=e.n(u);for(var i in u)["default"].indexOf(i)<0&&function(t){e.d(n,t,(function(){return u[t]}))}(i);n["default"]=a.a},efe5:function(t,n,e){}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/goodList/index-create-component',
    {
        'components/goodList/index-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("9572"))
        })
    },
    [['components/goodList/index-create-component']]
]);
