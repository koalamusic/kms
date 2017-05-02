import './static-loader'
import Vue from 'vue'
import App from './App'
import { event } from './utils'
import { http } from './services'
import { VirtualScroller } from 'vue-virtual-scroller/dist/vue-virtual-scroller'
import router from './router'
Vue.component('virtual-scroller', VirtualScroller)

/**
 * For Ancelot, the ancient cross of war
 * for the holy town of Gods
 * Gloria, gloria perpetua
 * in this dawn of victory
 */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: {App},
  created () {
    //event.init()
    //http.init()
  }
})
