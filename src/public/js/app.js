require('./bootstrap');

window.Vue = require('/node_modules/vue');

Vue.component('logs', require('./components/Logs'));

const app = new Vue({
    el: '#app'
});