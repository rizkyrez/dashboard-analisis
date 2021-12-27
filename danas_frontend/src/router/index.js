import { createRouter, createWebHistory } from 'vue-router'
import store from '../store'
import Home from '../views/Home'
import trendcs from '../views/calonSiswa/Trendcs'
import agama from '../views/calonSiswa/Agamav'
import area from '../views/calonSiswa/Areav'
import inexask from '../views/calonSiswa/Asalsekolahcs'
import achievement from '../views/calonSiswa/Achievementcs'
import csbachievement from '../views/calonSiswa/CsbAchievement'

import trendsiswa from '../views/siswa/TrendSiswa'
import classoccupancy from '../views/siswa/ClassOccupancy'
import agamasiswa from '../views/siswa/AgamaSiswa'
import StatisClassTingkat from '../views/siswa/StatisClassTingkat'
import sibling from '../views/siswa/SiblingSiswa'

// import trendsiswa from '../views/siswa/TrendSiswa'
// import classoccupancy from '../views/siswa/ClassOccupancy'
import agamapegawai from '../views/pegawai/AgamaPegawai'
// import StatisClassTingkat from '../views/siswa/StatisClassTingkat'
// import sibling from '../views/siswa/SiblingSiswa'
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
    path: '/calon-siswa/internal-vs-external',
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
      title: 'Dashboard - Calon Siswa - CSB Potensial VS Achievement',
      requireLogin: true
    },
    path: '/calon-siswa/csb-potensial-vs-achievement',
    name: 'CsbAchievement',
    component: csbachievement
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
      title: 'Dashboard - Siswa - Class Occupancy',
      requireLogin: true
    },
    path: '/siswa/class-occupancy',
    name: 'ClassOccupancy',
    component: classoccupancy
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
  {
    meta: {
      title: 'Dashboard - Siswa - Siblings',
      requireLogin: true
    },
    path: '/siswa/siblings',
    name: 'SiblingSiswa',
    component: sibling
  },
  {
    meta: {
      title: 'Dashboard - Kepegawaian - Agama',
      requireLogin: true
    },
    path: '/kepegawaian/agama',
    name: 'AgamaPegawai',
    component: agamapegawai
  },
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
