import { createRouter, createWebHistory } from 'vue-router'
import store from '../store'
import Home from '../views/Home'
import trendcs from '../views/calonSiswa/Trendcs'
import agama from '../views/calonSiswa/Agamav'
import area from '../views/calonSiswa/Areav'
import achievement from '../views/calonSiswa/Achievementcs'
/* eslint-disable */
const routes = [
  {
    meta: {
      title: 'Dashboard',
      requireLogin: true
    },
    path: '/dashboard',
    name: 'home',
    component: Home
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Trend',
      requireLogin: true
    },
    path: '/tjcs',
    name: 'TrendCalonSiswa',
    component: trendcs
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Agama',
      requireLogin: true
    },
    path: '/agama',
    name: 'Agama',
    component: agama
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Area',
      requireLogin: true
    },
    path: '/area',
    name: 'Area',
    component: area
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Achievement',
      requireLogin: true
    },
    path: '/achievement',
    name: 'Achievement',
    component: achievement
  },
  {
    meta: {
      title: 'Login',
      fullScreen: true
    },
    path: '/',
    name: 'Login',
    component: () => import(/* webpackChunkName: "login" */ '../views/Login')
  },
  {
    meta: {
      title: 'Error',
      fullScreen: true
    },
    path: '/error',
    name: 'error',
    component: () => import(/* webpackChunkName: "error" */ '../views/Error')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requireLogin) && !store.state.isAuthenticated) {
    next({ name: 'Login', query: {to: to.path } });
  } else {
    next()
  }
})

export default router
