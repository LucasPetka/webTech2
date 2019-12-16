require('./bootstrap');

window.Vue = require('vue');


import BootstrapVueTreeview from 'bootstrap-vue-treeview'
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'

Vue.component('hierarchy', require('./components/Hierarchy.vue').default);
Vue.component('vue-bootstrap-typeahead', VueBootstrapTypeahead);


Vue.use(BootstrapVueTreeview);



const app = new Vue({
    el: '#app',
});
