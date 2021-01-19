(function(t){function e(e){for(var n,l,o=e[0],s=e[1],c=e[2],d=0,f=[];d<o.length;d++)l=o[d],Object.prototype.hasOwnProperty.call(r,l)&&r[l]&&f.push(r[l][0]),r[l]=0;for(n in s)Object.prototype.hasOwnProperty.call(s,n)&&(t[n]=s[n]);u&&u(e);while(f.length)f.shift()();return a.push.apply(a,c||[]),i()}function i(){for(var t,e=0;e<a.length;e++){for(var i=a[e],n=!0,o=1;o<i.length;o++){var s=i[o];0!==r[s]&&(n=!1)}n&&(a.splice(e--,1),t=l(l.s=i[0]))}return t}var n={},r={app:0},a=[];function l(e){if(n[e])return n[e].exports;var i=n[e]={i:e,l:!1,exports:{}};return t[e].call(i.exports,i,i.exports,l),i.l=!0,i.exports}l.m=t,l.c=n,l.d=function(t,e,i){l.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:i})},l.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},l.t=function(t,e){if(1&e&&(t=l(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(l.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)l.d(i,n,function(e){return t[e]}.bind(null,n));return i},l.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return l.d(e,"a",e),e},l.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},l.p="/";var o=window["webpackJsonp"]=window["webpackJsonp"]||[],s=o.push.bind(o);o.push=e,o=o.slice();for(var c=0;c<o.length;c++)e(o[c]);var u=s;a.push([0,"chunk-vendors"]),i()})({0:function(t,e,i){t.exports=i("56d7")},"0c78":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"counter"}},[i("span",{staticClass:"counter"},[t._v(t._s(t._f("trans")("total_results"))+" "+t._s(t.countAllLabel))])])},r=[],a=i("5ab6"),l={name:"Counter",data:function(){return{countAll:0}},computed:{countAllLabel:function(){return this.countAll?this.countAll:"--"}},created:function(){var t=this;a["a"].on("totalCountUpdated",(function(e){t.countAll=e}))}},o=l,s=i("2877"),c=Object(s["a"])(o,n,r,!1,null,null,null);e["default"]=c.exports},"510a":function(t,e,i){"use strict";i("7d48")},5437:function(t,e,i){"use strict";i.r(e);i("99af"),i("4de4");var n,r,a=function(){var t=this,e=t.$createElement;t._self._c;return t._m(0)},l=[function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"overlay"},[i("div",{staticClass:"lds-dual-ring"})])}],o={name:"Loader"},s=o,c=(i("e0d1"),i("2877")),u=Object(c["a"])(s,a,l,!1,null,"2d51539e",null),d=u.exports,f=i("d4ec"),p=i("bee2"),h=i("bc3a"),v=i.n(h),b=i("b944"),m=function(){function t(e,i){Object(f["a"])(this,t),this.listUrl=e,this.filterUrl=i}return Object(p["a"])(t,[{key:"loadProducts",value:function(t){return v.a.post(this.listUrl,Object(b["a"])({tx_pxaproductmanager_lazyloading:{demand:t}}))}},{key:"loadAvailableOptions",value:function(t){return v.a.post(this.filterUrl,Object(b["a"])({tx_pxaproductmanager_lazyavailablefilters:{demand:t}}))}}]),t}(),g=m,y=(i("b64b"),function(){function t(e){Object(f["a"])(this,t),this.storagePid=e.storagePid,this.pageTreeStartingPoint=e.pageTreeStartingPoint,this.orderBy=e.orderBy,this.orderDirection=e.orderDirection,this.filterConjunction=e.filterConjunction,this.hideFilterOptionsNoResult=parseInt(e.hideFilterOptionsNoResult),this.limit=parseInt(e.limit),this.filters=e.filters||{},this.offSet=parseInt(e.offSet||0)}return Object(p["a"])(t,[{key:"updateFilter",value:function(t,e){var i=t.uid;if(0!==Object.keys(e).length)for(var n in this.filters[i]={conjunction:t.conjunction,attribute:t.attributeUid,type:t.type,value:[]},e){var r=e[n].value;this.filters[i].value.push(r)}else delete this.filters[i]}},{key:"hasQueryStringChanges",value:function(){return Object.keys(this.filters).length>0||this.offSet>0}},{key:"asQueryParams",value:function(){var t={};for(var e in this){var i=this[e];t[e]="filters"===e?JSON.stringify(i):i}return t}},{key:"updateOrderby",value:function(t,e){this.orderBy=t,this.orderDirection=e}}]),t}()),O=y,_=i("5ab6"),C=i("72bf"),k=i.n(C),A={arrayFormat:"comma",encode:!1},j={name:"LazyLoading",props:{listEndpoint:{type:String,required:!0},filterEndpoint:{type:String,required:!0},settings:{type:Object,required:!0}},components:{Loader:d},data:function(){var t=this.parseSettingsFromHash();return{demand:new O(t||this.settings),request:new g(this.listEndpoint,this.filterEndpoint),initialOffSet:parseInt(t?t.offSet:0),loading:!0,nextQueueLoading:!1,products:[],countAll:0}},computed:{hasMore:function(){return this.settings.limit>0&&this.products.length<this.countAll},countAllLabel:function(){return this.countAll?this.countAll:"--"},loadMoreText:function(){var t=this.nextQueueLoading?"loading":"load_more";return this.$options.filters.trans(t)}},created:function(){var t=this;this.initLoad(),_["a"].on("filterUpdate",(function(e){t.demand.updateFilter(e.filter,e.options),t.demand.offSet=0,t.initLoad(),t.updateQueryString()})),_["a"].on("filterUpdateDemand",(function(e){t.demand.updateFilter(e.filter,e.options)})),_["a"].on("filtersCleared",(function(){t.demand.offSet=0,t.initLoad(),t.updateQueryString()})),_["a"].on("sortingUpdate",(function(e){t.demand.updateOrderby(e.orderBy,e.orderDirection),t.demand.offSet=0,t.initLoad(),t.updateQueryString()}))},methods:{parseSettingsFromHash:function(){var t=window.location.hash;if(""!==t){var e=k.a.parse(t,A);return"undefined"===typeof e.filters?null:(e.filters=JSON.parse(e.filters),_["a"].emit("filterPreSelect",e.filters),_["a"].emit("sortingPreSelect",[e.orderBy,e.orderDirection]),e)}return null},initLoad:function(){var t=this;this.loading=!0,this.countAll=0;var e=Object.assign({},this.demand);e.offSet=0,this.settings.limit&&this.initialOffSet&&(e.limit+=this.initialOffSet,this.initialOffSet=0);var i=this.request.loadAvailableOptions(e);_["a"].emit("totalCountUpdated",""),this.request.loadProducts(e).then((function(e){var n=e.data;t.products=n.products,t.loading=!1,t.updateAvailableOptions(i)})).catch((function(t){return console.error("Error while request products:",t)}))},updateAvailableOptions:function(t){var e=this;t.then((function(t){var i=t.data;e.countAll=i.countAll,_["a"].emit("filterOptionsUpdate",i.options),_["a"].emit("totalCountUpdated",i.countAll)})).catch((function(t){return console.error("Error while request filter options:",t)}))},loadMore:function(){var t=this;this.nextQueueLoading=!0,this.demand.offSet+=this.settings.limit,this.request.loadProducts(this.demand).then((function(e){var i=e.data;t.products=t.products.concat(i.products),t.updateQueryString(),t.nextQueueLoading=!1})).catch((function(t){return console.error("Error while request products:",t)}))},updateQueryString:function(){var t="-";this.demand.hasQueryStringChanges()&&(t=k.a.stringify(this.demand.asQueryParams(),A)),window.location.hash=t}}},x=j,S=(i("510a"),Object(c["a"])(x,n,r,!1,null,null,null));e["default"]=S.exports},"56d7":function(t,e,i){"use strict";i.r(e);i("4de4"),i("e260"),i("e6cf"),i("cca6"),i("a79d");var n=i("a026"),r=i("d4ec"),a=i("bee2"),l=function(){function t(){Object(r["a"])(this,t)}return Object(a["a"])(t,null,[{key:"translate",value:function(t){return t="js."+t,TYPO3["lang"][t]||t}}]),t}(),o=l;n["a"].config.productionTip=!1,n["a"].component("lazy-loading",i("5437").default),n["a"].component("lazy-filter",i("9cfc").default),n["a"].component("lazy-checkbox-filter",i("d959").default),n["a"].component("lazy-radio-filter",i("f2d5").default),n["a"].component("clear-all",i("ecb5").default),n["a"].component("sorting",i("8864").default),n["a"].component("counter",i("0c78").default),n["a"].filter("trans",(function(t){return t?o.translate(t):""})),document.getElementById("pm-lazy-loading-app")&&(window.pmLazyLoadingApp=new n["a"]({el:"#pm-lazy-loading-app"}))},"5ab6":function(t,e,i){"use strict";var n,r=i("d4ec"),a=i("bee2"),l=i("a026"),o=function(){function t(){return Object(r["a"])(this,t),null===n&&(n=this),this.vue=new l["a"],n}return Object(a["a"])(t,[{key:"emit",value:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;this.vue.$emit(t,e)}},{key:"on",value:function(t,e){this.vue.$on(t,e)}}]),t}();e["a"]=new o},"7d48":function(t,e,i){},"7e45":function(t,e,i){},8864:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"sorting"}},[i("span",[t._v(t._s(t._f("trans")("sort_by")))]),i("select",{directives:[{name:"model",rawName:"v-model",value:t.value,expression:"value"}],on:{change:[function(e){var i=Array.prototype.filter.call(e.target.options,(function(t){return t.selected})).map((function(t){var e="_value"in t?t._value:t.value;return e}));t.value=e.target.multiple?i:i[0]},t.sortingUpdate]}},t._l(t.options,(function(e){return i("option",{key:e.value,domProps:{value:e.value}},[t._v(" "+t._s(e.text)+" ")])})),0)])},r=[],a=(i("a15b"),i("ac1f"),i("1276"),i("5ab6")),l={name:"Sorting",props:{options:Array,settings:{type:Object,required:!0}},data:function(){return{value:""}},created:function(){var t=this;a["a"].on("sortingPreSelect",(function(e){return t.preselectOption(e)}));var e=[this.settings.orderBy,this.settings.orderDirection];this.preselectOption(e)},methods:{sortingUpdate:function(){var t=this.value.split(","),e=t[0],i=t[1];a["a"].emit("sortingUpdate",{orderBy:e,orderDirection:i})},preselectOption:function(t){var e=t.join(",");this.value=e}}},o=l,s=i("2877"),c=Object(s["a"])(o,n,r,!1,null,null,null);e["default"]=c.exports},"8cd6":function(t,e,i){},"9cfc":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("multiselect",{attrs:{options:t.options,multiple:!0,"track-by":"value",label:"label",placeholder:t.placeholder},on:{input:t.emitUpdate},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}})],1)},r=[],a=(i("4de4"),i("caad"),i("2532"),i("8e5f")),l=i.n(a),o=i("5ab6"),s={name:"LazyFilter",components:{Multiselect:l.a},props:{filter:Object},data:function(){return{value:null,options:this.filter.options}},computed:{placeholder:function(){return this.filter.label||this.$options.filters.trans("please_select")}},created:function(){var t=this;o["a"].on("filterPreSelect",(function(e){return t.preselectOptions(e)})),o["a"].on("filterOptionsUpdate",(function(e){return t.updateAvailableOptions(e)})),o["a"].on("clear-all",(function(){return t.clearAllChecked()}))},methods:{clearAllChecked:function(){this.value=[],o["a"].emit("filterUpdateDemand",{filter:this.filter,options:this.value})},emitUpdate:function(){o["a"].emit("filterUpdate",{filter:this.filter,options:this.value})},preselectOptions:function(t){var e=this.filter.uid;if("undefined"!==typeof t[e]){var i=t[e].value;i&&(this.value=this.filter.options.filter((function(t){return i.includes(t.value)})))}},updateAvailableOptions:function(t){if(null!==t){var e=t[this.filter.uid]||t["and"];this.options=this.filter.options.filter((function(t){return e.includes(t.value)}))}else this.options=this.filter.options}}},c=s,u=(i("60bc"),i("2877")),d=Object(u["a"])(c,n,r,!1,null,null,null);e["default"]=d.exports},"9fcf":function(t,e,i){},b1ce:function(t,e,i){"use strict";i("7e45")},d244:function(t,e,i){"use strict";i("9fcf")},d959:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"filterCheckbox"}},[i("div",{staticClass:"checkbox-filter-wrapper",class:t.accordionClasses},[i("div",{staticClass:"checkbox-filter-header",on:{click:t.toggleAccordion}},[i("span",{staticClass:"toggle-icon"}),i("span",{staticClass:"filter-name"},[t._v(t._s(t.placeholder))])]),i("div",{staticClass:"checkbox-filter-body"},[i("div",{staticClass:"checkbox-filter-content"},[t._l(t.options,(function(e){return i("div",{key:e.value},[i("input",{directives:[{name:"model",rawName:"v-model",value:t.value,expression:"value"}],staticClass:"checkbox-filter-check",attrs:{id:e.value+e.label,type:"checkbox"},domProps:{value:e,checked:Array.isArray(t.value)?t._i(t.value,e)>-1:t.value},on:{change:[function(i){var n=t.value,r=i.target,a=!!r.checked;if(Array.isArray(n)){var l=e,o=t._i(n,l);r.checked?o<0&&(t.value=n.concat([l])):o>-1&&(t.value=n.slice(0,o).concat(n.slice(o+1)))}else t.value=a},t.emitUpdate]}}),i("label",{staticClass:"checkbox-filter-label",attrs:{for:e.value+e.label,options:e.label},domProps:{textContent:t._s(e.label)}}),i("br")])})),i("button",{staticClass:"btn-clear",on:{click:t.clearChecked}},[t._v(" "+t._s(t._f("trans")("clear"))+" ")])],2)])])])},r=[],a=(i("4de4"),i("caad"),i("2532"),i("5ab6")),l={name:"FilterCheckbox",props:{filter:Object},data:function(){return{value:[],options:this.filter.options,isOpen:null}},computed:{placeholder:function(){return this.filter.label||this.$options.filters.trans("please_select")},accordionClasses:function(){return{"is-closed":!this.isOpen,"is-open":this.isOpen,"is-static":"plain"==this.filter.gui_state}}},created:function(){var t=this;a["a"].on("filterPreSelect",(function(e){return t.preselectOptions(e)})),a["a"].on("filterOptionsUpdate",(function(e){return t.updateAvailableOptions(e)})),a["a"].on("clear-all",(function(){return t.clearAllChecked()})),this.checkAccordionCollapsed()},methods:{toggleAccordion:function(){"plain"!==this.filter.gui_state&&(this.isOpen=!this.isOpen)},checkAccordionCollapsed:function(){"collapsed"==this.filter.gui_state?this.isOpen=!1:this.isOpen=!0},clearChecked:function(){this.value=[],a["a"].emit("filterUpdate",{filter:this.filter,options:this.value})},clearAllChecked:function(){this.value=[],a["a"].emit("filterUpdateDemand",{filter:this.filter,options:this.value})},emitUpdate:function(){a["a"].emit("filterUpdate",{filter:this.filter,options:this.value})},preselectOptions:function(t){var e=this.filter.uid;if("undefined"!==typeof t[e]){var i=t[e].value;i&&(this.value=this.filter.options.filter((function(t){return i.includes(t.value)})))}},updateAvailableOptions:function(t){if(null!==t){var e=t[this.filter.uid]||t["and"];this.options=this.filter.options.filter((function(t){return e.includes(t.value)}))}else this.options=this.filter.options}}},o=l,s=(i("d244"),i("2877")),c=Object(s["a"])(o,n,r,!1,null,"4f4c9dc0",null);e["default"]=c.exports},e0d1:function(t,e,i){"use strict";i("8cd6")},ecb5:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"clearAllButton"}},[i("button",{staticClass:"btn-clear-all",on:{click:t.clearChecked}},[t._v(t._s(t._f("trans")("clear_all")))])])},r=[],a=i("5ab6"),l={name:"ClearAllButton",methods:{clearChecked:function(){a["a"].emit("clear-all"),a["a"].emit("filtersCleared")}}},o=l,s=i("2877"),c=Object(s["a"])(o,n,r,!1,null,null,null);e["default"]=c.exports},f2d5:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"filterRadioButton"}},[i("div",{staticClass:"radiobutton-filter-wrapper",class:t.accordionClasses},[i("div",{staticClass:"radiobutton-filter-header",on:{click:t.toggleAccordion}},[i("span",{staticClass:"toggle-icon"}),i("span",{staticClass:"filter-name"},[t._v(t._s(t.placeholder))])]),i("div",{staticClass:"radiobutton-filter-body"},[i("div",{staticClass:"radiobutton-filter-content"},[t._l(t.options,(function(e){return i("div",{key:e.value},[i("input",{directives:[{name:"model",rawName:"v-model",value:t.value,expression:"value"}],staticClass:"radiobutton-filter-check",attrs:{id:e.value+e.label,type:"radio"},domProps:{value:e,checked:t._q(t.value,e)},on:{change:[function(i){t.value=e},t.emitUpdate]}}),i("label",{staticClass:"radiobutton-filter-label",attrs:{for:e.value+e.label,options:e.label},domProps:{textContent:t._s(e.label)}}),i("br")])})),i("button",{staticClass:"btn-clear",on:{click:t.clearChecked}},[t._v(" "+t._s(t._f("trans")("clear")))])],2)])])])},r=[],a=(i("4de4"),i("caad"),i("2532"),i("5ab6")),l={name:"FilterRadioButton",props:{filter:Object},data:function(){return{value:[],options:this.filter.options,isOpen:null}},computed:{placeholder:function(){return this.filter.label||this.$options.filters.trans("please_select")},accordionClasses:function(){return{"is-closed":!this.isOpen,"is-open":this.isOpen,"is-static":"plain"==this.filter.gui_state}}},created:function(){var t=this;a["a"].on("filterPreSelect",(function(e){return t.preselectOptions(e)})),a["a"].on("filterOptionsUpdate",(function(e){return t.updateAvailableOptions(e)})),a["a"].on("clear-all",(function(){return t.clearAllChecked()})),this.checkAccordionCollapsed()},methods:{toggleAccordion:function(){"plain"!==this.filter.gui_state&&(this.isOpen=!this.isOpen)},checkAccordionCollapsed:function(){"collapsed"==this.filter.gui_state?this.isOpen=!1:this.isOpen=!0},clearChecked:function(){this.value=[],a["a"].emit("filterUpdate",{filter:this.filter,options:this.value})},clearAllChecked:function(){this.value=[],a["a"].emit("filterUpdateDemand",{filter:this.filter,options:this.value})},emitUpdate:function(){a["a"].emit("filterUpdate",{filter:this.filter,options:[this.value]})},preselectOptions:function(t){var e=this.filter.uid;if("undefined"!==typeof t[e]){var i=t[e].value;i&&(this.value=this.filter.options.filter((function(t){return i.includes(t.value)})))}},updateAvailableOptions:function(t){if(null!==t){var e=t[this.filter.uid]||t["and"];this.options=this.filter.options.filter((function(t){return e.includes(t.value)}))}else this.options=this.filter.options}}},o=l,s=(i("b1ce"),i("2877")),c=Object(s["a"])(o,n,r,!1,null,"d2401b8c",null);e["default"]=c.exports}});
//# sourceMappingURL=app.js.map