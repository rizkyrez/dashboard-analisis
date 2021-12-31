import axios from 'axios'
import { setHeaderToken, removeHeaderToken } from '../utils/auth'
export default {
    state: {
        isAuthenticated: false,
        user: null
    },
    mutations: {
        set_user (state, data) {
            state.user = data
            state.isAuthenticated = true
        },
        reset_user (state) {
            state.user = null
            state.isAuthenticated = false
        }
    },
    getters: {
        isAuthenticated (state) {
            return state.isAuthenticated
        },
        user (state) {
            return state.user
        }
    },
    actions: {
        login ({ dispatch, commit }, data) {
            return new Promise((resolve, reject) => {
                axios.post('auth/login/', data)
                .then(response => {
                    const token = response.data.access_token
                    localStorage.setItem('token', token)
                    setHeaderToken(token)
                    commit('set_user', response.data.user)
                    resolve(response)
                })
                .catch(err => {
                    commit('reset_user')
                    localStorage.removeItem('token')
                    reject(err)
                })
            })
        },
        async get_user ({ commit }) {
            if (!localStorage.getItem('token')) {
                return
            }
            try {
                const response = await axios.get('users')
                commit('set_user', response.data.data)
            } catch (error) {
                commit('reset_user')
                removeHeaderToken()
                localStorage.removeItem('token')
                return error
            }
        },
        logout ({ commit }) {
            return new Promise((resolve) => {
                commit('reset_user')
                localStorage.removeItem('token')
                removeHeaderToken()
                resolve()
            })
        }
    }
}
