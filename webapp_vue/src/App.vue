<template>
  <nav-bar/>
  <aside-menu :menu="menu"/>
  <router-view/>
  <footer-bar/>
  <overlay v-show="isAsideLgActive" z-index="z-30" @overlay-click="overlayClick" />
</template>

<script>
/* eslint-disable */
// @ is an alias to /src
import { computed } from 'vue'
import { useStore } from 'vuex'
import axios from 'axios'
import menu from '@/menu.js'
import NavBar from '@/components/NavBar'
import AsideMenu from '@/components/AsideMenu'
import FooterBar from '@/components/FooterBar'
import Overlay from '@/components/Overlay'

export default {
  components: {
    Overlay,
    FooterBar,
    AsideMenu,
    NavBar
  },
  setup () {
    const store = useStore()

    // store.commit('user', {
    //   name: 'John Doe',
    //   email: 'john@example.com',
    //   avatar: 'https://avatars.dicebear.com/api/avataaars/example.svg?options[top][]=shortHair&options[accessoriesChance]=93'
    // })

    const isAsideLgActive = computed(() => store.state.isAsideLgActive)

    const overlayClick = () => {
      store.dispatch('asideLgToggle', false)
    }

    return {
      menu,
      isAsideLgActive,
      overlayClick
    }
  },
  beforeCreate () {
      this.$store.commit('initializeStore')

      const token = this.$store.state.token

      if (token) {
        axios.defaults.headers.common['Authorization'] = "Bearer " + token
      } else {
        axios.defaults.headers.common['Authorization'] = ""
      }
  },
}
</script>
