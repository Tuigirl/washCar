import Vue from 'vue';
import FastClick from 'fastclick';
import App from './App'
import router from './router'
import store from './store'
import { ToastPlugin, ConfirmPlugin, AlertPlugin } from 'vux'
import AjaxPlugin from '@/utils/fetch.js'
import provide from '@/provide/main.js'
// import  { WechatPlugin } from 'vux'

Vue.use(ToastPlugin) //Toast
Vue.use(AjaxPlugin) //Ajax
Vue.use(ConfirmPlugin) //ConfirmPlugin
Vue.use(AlertPlugin) //ConfirmPlugin
// Vue.use(WechatPlugin) //微信JSSDK

FastClick.attach(document.body)

/**
 * 日志输出开关
 */
Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  router,
  store,
  provide,
  render: h => h(App)
}).$mount('#app-box')
