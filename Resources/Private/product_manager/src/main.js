import Vue from 'vue';

Vue.config.productionTip = false;

Vue.component('lazy-loading', require('./components/LazyLoading/LazyLoading').default);
Vue.component('lazy-filter', require('./components/LazyLoading/Filter').default);

if (document.getElementById('pm-lazy-loading-app')) {
    window.pmLazyLoadingApp = new Vue({
        el: '#pm-lazy-loading-app',

        data: {
            pageLoading: true
        },
    });
}
