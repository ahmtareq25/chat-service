require('./bootstrap');
import Vue from 'vue';
import VueRouter from 'vue-router'
window.Vue = require('vue').default;
import Welcome from './components/Welcome'
import Messages from './components/Messages'
window.EventBus=new Vue();
Vue.use(VueRouter)
var router=new VueRouter({
    mode:'history',
    routes:[
        {
            path:'/chat',
            component:Welcome,
            name:'welcome'
        },
        {
            path:'/chat/:id',
            props:true,
            component:Messages
        },
    ]
});


Vue.component('chat', require('./components/Chat.vue').default);

const app = new Vue({
    el: '#app',
    router
});
