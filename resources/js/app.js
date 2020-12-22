require('./bootstrap');

window.Vue = require('vue');

Vue.component('products', require('./components/Products.vue').default);

const app = new Vue({
    el: '#app',
});
