import Vue from 'vue';
import Translate from "./utility/Translate";

Vue.config.productionTip = false;

Vue.component('lazy-loading', require('./components/LazyLoading/LazyLoading').default);
Vue.component('lazy-filter', require('./components/LazyLoading/Filter').default);
Vue.component('lazy-checkbox-filter', require('./components/LazyLoading/FilterCheckbox').default);
Vue.component('lazy-radio-filter', require('./components/LazyLoading/FilterRadioButton').default);

Vue.filter('trans', key => {
    if (!key) {
        return '';
    }

    return Translate.translate(key);
});

if (document.getElementById('pm-lazy-loading-app')) {
    window.pmLazyLoadingApp = new Vue({
        el: '#pm-lazy-loading-app',
    });
}
