(function(t){function e(e){for(var i,o,u=e[0],s=e[1],l=e[2],f=0,d=[];f<u.length;f++)o=u[f],Object.prototype.hasOwnProperty.call(r,o)&&r[o]&&d.push(r[o][0]),r[o]=0;for(i in s)Object.prototype.hasOwnProperty.call(s,i)&&(t[i]=s[i]);c&&c(e);while(d.length)d.shift()();return a.push.apply(a,l||[]),n()}function n(){for(var t,e=0;e<a.length;e++){for(var n=a[e],i=!0,u=1;u<n.length;u++){var s=n[u];0!==r[s]&&(i=!1)}i&&(a.splice(e--,1),t=o(o.s=n[0]))}return t}var i={},r={app:0},a=[];function o(e){if(i[e])return i[e].exports;var n=i[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.m=t,o.c=i,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)o.d(n,i,function(e){return t[e]}.bind(null,i));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="/";var u=window["webpackJsonp"]=window["webpackJsonp"]||[],s=u.push.bind(u);u.push=e,u=u.slice();for(var l=0;l<u.length;l++)e(u[l]);var c=s;a.push([0,"chunk-vendors"]),n()})({0:function(t,e,n){t.exports=n("56d7")},"510a":function(t,e,n){"use strict";var i=n("7d48"),r=n.n(i);r.a},5437:function(t,e,n){"use strict";n.r(e);n("99af"),n("4de4");var i,r,a=function(){var t=this,e=t.$createElement;t._self._c;return t._m(0)},o=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"overlay"},[n("div",{staticClass:"lds-dual-ring"})])}],u={name:"Loader"},s=u,l=(n("e0d1"),n("2877")),c=Object(l["a"])(s,a,o,!1,null,"2d51539e",null),f=c.exports,d=(n("caad"),n("2532"),n("d4ec")),p=n("bee2"),h=n("bc3a"),v=n.n(h),m=n("d022"),b=function(){function t(e){Object(d["a"])(this,t),this.url=e}return Object(p["a"])(t,[{key:"loadProducts",value:function(t){return this._submitDemand(this.url,t)}},{key:"loadAvailableOptions",value:function(t){return this._submitDemand(this._getActionUrl("list","LazyAvailableFilters"),t)}},{key:"_submitDemand",value:function(t,e){return v.a.post(t,Object(m["a"])({tx_pxaproductmanager_pi1:{demand:e}}))}},{key:"_getActionUrl",value:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,n="&tx_pxaproductmanager_pi1[action]="+t;e&&(n+="&tx_pxaproductmanager_pi1[controller]=Api\\"+e);var i=this.url;return i.includes("?")?i+n:i+n.substring(1)}}]),t}(),g=b,y=(n("b64b"),function(){function t(e){Object(d["a"])(this,t),this.categories=e.categories,this.storagePid=e.storagePid,this.orderBy=e.orderBy,this.orderDirection=e.orderDirection,this.filterConjunction=e.filterConjunction,this.hideFilterOptionsNoResult=parseInt(e.hideFilterOptionsNoResult),this.limit=parseInt(e.limit),this.filters=e.filters||{},this.offSet=parseInt(e.offSet||0)}return Object(p["a"])(t,[{key:"updateFilter",value:function(t,e){var n=t.uid;if(0!==Object.keys(e).length)for(var i in this.filters[n]={conjunction:t.conjunction,attribute:t.attributeUid,type:t.type,value:[]},e){var r=e[i].value;this.filters[n].value.push(r)}else delete this.filters[n]}},{key:"hasQueryStringChanges",value:function(){return Object.keys(this.filters).length>0||this.offSet>0}},{key:"asQueryParams",value:function(){var t={};for(var e in this){var n=this[e];t[e]="filters"===e?JSON.stringify(n):n}return t}}]),t}()),O=y,j=n("5ab6"),S=n("72bf"),_=n.n(S),w={arrayFormat:"comma",encode:!1},x={name:"LazyLoading",props:{endpoint:{type:String,required:!0},settings:{type:Object,required:!0}},components:{Loader:f},data:function(){var t=this.parseSettingsFromHash();return{demand:new O(t||this.settings),request:new g(this.endpoint),initialOffSet:parseInt(t?t.offSet:0),loading:!0,nextQueueLoading:!1,products:[],countAll:0}},computed:{hasMore:function(){return this.settings.limit>0&&this.products.length<this.countAll},countAllLabel:function(){return this.countAll?this.countAll:"--"},loadMoreText:function(){var t=this.nextQueueLoading?"loading":"load_more";return this.$options.filters.trans(t)}},created:function(){var t=this;this.initLoad(),j["a"].on("filterUpdate",(function(e){t.demand.updateFilter(e.filter,e.options),t.demand.offSet=0,t.initLoad(),t.updateQueryString()}))},methods:{parseSettingsFromHash:function(){var t=window.location.hash;if(""!==t){var e=_.a.parse(t,w);return e.filters=JSON.parse(e.filters),j["a"].emit("filterPreSelect",e.filters),e}return null},initLoad:function(){var t=this;this.loading=!0;var e=Object.assign({},this.demand);e.offSet=0,this.settings.limit&&this.initialOffSet&&(e.limit+=this.initialOffSet,this.initialOffSet=0),this.request.loadProducts(e).then((function(e){var n=e.data;t.products=n.products,t.loading=!1})).catch((function(t){return console.error("Error while request products:",t)})),this.request.loadAvailableOptions(e).then((function(e){var n=e.data;t.countAll=n.countAll,j["a"].emit("filterOptionsUpdate",n.options)})).catch((function(t){return console.error("Error while request products:",t)}))},loadMore:function(){var t=this;this.nextQueueLoading=!0,this.demand.offSet+=this.settings.limit,this.request.loadProducts(this.demand).then((function(e){var n=e.data;t.products=t.products.concat(n.products),t.updateQueryString(),t.nextQueueLoading=!1})).catch((function(t){return console.error("Error while request products:",t)}))},updateQueryString:function(){var t="";this.demand.hasQueryStringChanges()&&(t=_.a.stringify(this.demand.asQueryParams(),w)),window.location.hash=t}}},k=x,P=(n("510a"),Object(l["a"])(k,i,r,!1,null,null,null));e["default"]=P.exports},"56d7":function(t,e,n){"use strict";n.r(e);n("4de4"),n("e260"),n("e6cf"),n("cca6"),n("a79d");var i=n("a026"),r=n("d4ec"),a=n("bee2"),o=function(){function t(){Object(r["a"])(this,t)}return Object(a["a"])(t,null,[{key:"translate",value:function(t){return t="js."+t,TYPO3["lang"][t]||t}}]),t}(),u=o;i["a"].config.productionTip=!1,i["a"].component("lazy-loading",n("5437").default),i["a"].component("lazy-filter",n("9cfc").default),i["a"].filter("trans",(function(t){return t?u.translate(t):""})),document.getElementById("pm-lazy-loading-app")&&(window.pmLazyLoadingApp=new i["a"]({el:"#pm-lazy-loading-app"}))},"5ab6":function(t,e,n){"use strict";var i,r=n("d4ec"),a=n("bee2"),o=n("a026"),u=function(){function t(){return Object(r["a"])(this,t),null===i&&(i=this),this.vue=new o["a"],i}return Object(a["a"])(t,[{key:"emit",value:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;this.vue.$emit(t,e)}},{key:"on",value:function(t,e){this.vue.$on(t,e)}}]),t}();e["a"]=new u},"7d48":function(t,e,n){},"8cd6":function(t,e,n){},"9cfc":function(t,e,n){"use strict";n.r(e);var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("multiselect",{attrs:{options:t.options,multiple:!0,"track-by":"value",label:"label",placeholder:t.translate("please_select")},on:{input:t.emitUpdate},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}})],1)},r=[],a=(n("4de4"),n("caad"),n("2532"),n("8e5f")),o=n.n(a),u=n("5ab6"),s={name:"LazyFilter",components:{Multiselect:o.a},props:{filter:Object},data:function(){return{value:null,options:this.filter.options}},created:function(){var t=this;u["a"].on("filterPreSelect",(function(e){return t.preselectOptions(e)})),u["a"].on("filterOptionsUpdate",(function(e){return t.updateAvailableOptions(e)}))},methods:{translate:function(t){return this.$options.filters.trans(t)},emitUpdate:function(){u["a"].emit("filterUpdate",{filter:this.filter,options:this.value})},preselectOptions:function(t){var e=this.filter.uid;if("undefined"!==typeof t[e]){var n=t[e].value;n&&(this.value=this.filter.options.filter((function(t){return n.includes(t.value)})))}},updateAvailableOptions:function(t){if(null!==t){var e=t[this.filter.uid]||t["and"];this.options=this.filter.options.filter((function(t){return e.includes(t.value)}))}else this.options=this.filter.options}}},l=s,c=(n("60bc"),n("2877")),f=Object(c["a"])(l,i,r,!1,null,null,null);e["default"]=f.exports},e0d1:function(t,e,n){"use strict";var i=n("8cd6"),r=n.n(i);r.a}});
//# sourceMappingURL=app.js.map