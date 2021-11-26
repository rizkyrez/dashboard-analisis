import { createRouter, createWebHistory } from 'vue-router'
import store from '../store'
import Home from '../views/Home'
import trendcs from '../views/calonSiswa/Trendcs'
import agama from '../views/calonSiswa/Agamav'
import area from '../views/calonSiswa/Areav'
import inexask from '../views/calonSiswa/Asalsekolahcs'
import achievement from '../views/calonSiswa/Achievementcs'

import trendsiswa from '../views/siswa/TrendSiswa'
import agamasiswa from '../views/siswa/AgamaSiswa'
import StatisClassTingkat from '../views/siswa/StatisClassTingkat'
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
    path: '/calon-siswa/trends',
    name: 'TrendCalonSiswa',
    component: trendcs
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Agama',
      requireLogin: true
    },
    path: '/calon-siswa/agama',
    name: 'Agamav',
    component: agama
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Area',
      requireLogin: true
    },
    path: '/calon-siswa/area',
    name: 'Area',
    component: area
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Internal VS External',
      requireLogin: true
    },
    path: '/calon-siswa/inex',
    name: 'Asalsekolah',
    component: inexask
  },
  {
    meta: {
      title: 'Dashboard - Calon Siswa - Achievement',
      requireLogin: true
    },
    path: '/calon-siswa/achievement',
    name: 'Achievement',
    component: achievement
  },
  {
    meta: {
      title: 'Dashboard - Siswa - Trend',
      requireLogin: true
    },
    path: '/siswa/trends',
    name: 'TrendSiswa',
    component: trendsiswa
  },
  {
    meta: {
      title: 'Dashboard - Siswa - Jumlah Kelas Per Tingkat',
      requireLogin: true
    },
    path: '/siswa/statistik-jumlah-kelas',
    name: 'StatisClassTingkat',
    component: StatisClassTingkat
  },
  {
    meta: {
      title: 'Dashboard - Siswa - Agama',
      requireLogin: true
    },
    path: '/siswa/agama',
    name: 'AgamaSiswa',
    component: agamasiswa
  },
//   {
//     meta: {
//       title: 'Dashboard - Siswa - Agama',
//       requireLogin: true
//     },
//     path: '/siswa/agama',
//     name: 'Agama',
//     component: agama
//   },
//   {
//     meta: {
//       title: 'Dashboard - Siswa - Area',
//       requireLogin: true
//     },
//     path: '/siswa/area',
//     name: 'Area',
//     component: area
//   },
//   {
//     meta: {
//       title: 'Dashboard - Siswa - Internal VS External',
//       requireLogin: true
//     },
//     path: '/siswa/inex',
//     name: 'Asalsekolah',
//     component: inexask
//   },
//   {
//     meta: {
//       title: 'Dashboard - Siswa - Achievement',
//       requireLogin: true
//     },
//     path: '/siswa/achievement',
//     name: 'Achievement',
//     component: achievement
//   },
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
