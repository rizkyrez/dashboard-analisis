/* eslint-disable */
import { createApp } from 'vue'

import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'

import './css/main.css'
import 'ag-grid-community/dist/styles/ag-grid.css'
import 'ag-grid-community/dist/styles/ag-theme-alpine.css'

axios.defaults.baseURL = 'http://127.0.0.1:8000'

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
