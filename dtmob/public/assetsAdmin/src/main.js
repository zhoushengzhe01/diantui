import Vue from 'vue'
import ElementUI from 'element-ui'
import History from './history.js'
import router from './router.js'
import 'element-ui/lib/theme-chalk/index.css'
import App from './App.vue'



import VueQuillEditor from 'vue-quill-editor'

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import './assets/less/main.less'

Vue.use(VueQuillEditor)



Vue.use(ElementUI);

//公共JS
import api from './config/api';
Vue.prototype.$api = api;

new Vue({
  router,
  el: '#app',
  render: h => h(App)
})
