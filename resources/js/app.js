require('./bootstrap');

window.Vue = require('vue');

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'


import BootstrapVueTreeview from 'bootstrap-vue-treeview'
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
import BootstrapVue from 'bootstrap-vue'

Vue.component('hierarchy', require('./components/Hierarchy.vue').default);
Vue.component('vue-bootstrap-typeahead', VueBootstrapTypeahead);


Vue.use(BootstrapVue)
Vue.use(BootstrapVueTreeview);



const app = new Vue({
    el: '#app',
});
