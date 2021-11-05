import { createRouter, createWebHashHistory } from 'vue-router'
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
      title: 'Tables'
    },
    path: '/tables',
    name: 'tables',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "tables" */ '../views/Tables')
  },
  {
    meta: {
      title: 'Forms'
    },
    path: '/forms',
    name: 'forms',
    component: () => import(/* webpackChunkName: "forms" */ '../views/Forms')
  },
  {
    meta: {
      title: 'Profile'
    },
    path: '/profile',
    name: 'profile',
    component: () => import(/* webpackChunkName: "profile" */ '../views/Profile')
  },
  {
    meta: {
      title: 'Ui'
    },
    path: '/ui',
    name: 'ui',
    component: () => import(/* webpackChunkName: "ui" */ '../views/Ui')
  },
  {
    meta: {
      title: 'Responsive layout'
    },
    path: '/responsive',
    name: 'responsive',
    component: () => import(/* webpackChunkName: "responsive" */ '../views/Responsive')
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
  history: createWebHashHistory(),
  routes,
  scrollBehavior (to, from, savedPosition) {
    return savedPosition || { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requireLogin) && !store.state.isAuthenticated) {
    next({ name: 'Login', query: {to: to.path } });
  } else {
    next()
  }
})

export default router
