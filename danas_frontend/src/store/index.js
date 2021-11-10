import { createStore } from 'vuex'
import axios from 'axios'
/* eslint-disable */
export default createStore({
  state: {

    isAuthenticated: false,
    token: null,
    // username: null,
    itemHeader: [],
    isLoading: false,

    /* fullScreen - fullscreen form layout (e.g. login page) */
    isFullScreen: false,

    /* Aside */
    isAsideMobileExpanded: false,
    // isAsideLgActive: false,

    /* Dark mode */
    darkMode: false,

    /* Field focus with ctrl+k (to register only once) */
    isFieldFocusRegistered: false,

    /* Sample data (commonly used) */
    clients: [],
    history: []
  },
  mutations: {
    initializeStore (state) {
      if (localStorage.getItem('token')) {
        state.token = localStorage.getItem('token')
        state.isAuthenticated = true
      } else {
        state.token = null
        state.isAuthenticated = false
      }
    },
    setIsLoading(state, status) {
      state.isLoading = status
    },
    setToken(state, token) {
      state.token = token
      state.isAuthenticated = true
    },

    removeToken(state) {
      state.token = null
      state.username = null
      state.isAuthenticated = false
    },
    /* A fit-them-all commit */
    basic (state, payload) {
      state[payload.key] = payload.value
    },

    getHeader(state, arr) {
        state.itemHeader.push(arr)
    }

    // /* User */
    // user (state, payload) {
    //   if (payload.name) {
    //     state.userName = payload.name
    //   }
    //   if (payload.email) {
    //     state.userEmail = payload.email
    //   }
    //   if (payload.avatar) {
    //     state.userAvatar = payload.avatar
    //   }
    // }
  },
  actions: {
    asideMobileToggle ({ commit, state }, payload = null) {
      const isShow = payload !== null ? payload : !state.isAsideMobileExpanded

      document.getElementById('app').classList[isShow ? 'add' : 'remove']('ml-60')

      document.documentElement.classList[isShow ? 'add' : 'remove']('m-clipped')

      commit('basic', {
        key: 'isAsideMobileExpanded',
        value: isShow
      })
    },

    asideLgToggle ({ commit, state }, payload = null) {
      commit('basic', { key: 'isAsideLgActive', value: payload !== null ? payload : !state.isAsideLgActive })
    },

    fullScreenToggle ({ commit, state }, value) {
      commit('basic', { key: 'isFullScreen', value })

      document.documentElement.classList[value ? 'add' : 'remove']('full-screen')
    },

    darkMode ({ commit, state }) {
      const value = !state.darkMode

      document.documentElement.classList[value ? 'add' : 'remove']('dark')

      commit('basic', {
        key: 'darkMode',
        value
      })
    },

    fetch ({ commit }, payload) {
      axios
        .get(`data-sources/${payload}.json`)
        .then((r) => {
          if (r.data) {
            if (r.data.data) {
              commit('basic', {
                key: payload,
                value: r.data.data
              })
            }
            if (r.data.status) {
              commit('basic', {
                key: `${payload}Status`,
                value: r.data.status
              })
            }
          }
        })
        .catch(error => {
          alert(error.message)
        })
    }
  },
  modules: {
  }
})
