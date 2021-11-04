/* eslint-disable */
import { createApp } from 'vue'
// import ECharts from 'vue-echarts' // refers to components/ECharts.vue in webpack

import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'

import 'echarts/lib/chart/bar'
import 'echarts/lib/component/tooltip'
import './css/main.css'

axios.defaults.baseURL = 'http://127.0.0.1:8000'
/* Fetch sample data */
store.dispatch('fetch', 'clients')
store.dispatch('fetch', 'history')

/* Dark mode */
// store.dispatch('darkMode')

/* Collapse mobile aside menu on route change */
router.beforeEach(to => {
  store.dispatch('asideMobileToggle', false)
  store.dispatch('asideLgToggle', false)
})

/* Default title tag */
const defaultDocumentTitle = 'Dashboard Report'

router.afterEach(to => {
  /* Set document title from route meta */
  if (to.meta && to.meta.title) {
    document.title = `${to.meta.title} | ${defaultDocumentTitle}`
  } else {
    document.title = defaultDocumentTitle
  }

  /* Full screen mode */
  store.dispatch('fullScreenToggle', !!to.meta.fullScreen)
})

createApp(App).use(store).use(router, axios).mount('#app')
