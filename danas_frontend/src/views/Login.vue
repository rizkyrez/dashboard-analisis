<template>

  <full-screen-section bg="login" v-slot="{ cardClass, cardRounded }">
    <card-component title="Login" :class="cardClass" :rounded="cardRounded" @submit.prevent="submit" form>

      <field label="Username" help="Please enter your login">
        <control v-model="username" :icon="mdiAccount" type="text" name="username" autocomplete="username"/>
      </field>

      <field label="Password" help="Please enter your password">
        <control v-model="password" :icon="mdiAsterisk" type="password" name="password" autocomplete="current-password"/>
      </field>

      <!-- <check-radio-picker name="remember" v-model="form.remember" :options="{ remember: 'Remember' }" /> -->
      <div class="bg-red-500 rounded-md p-2 text-white text-sm" v-if="errors.length">
          <p v-for="error in errors" v-bind:key="error">{{ error }}</p>
      </div>
      <divider />

      <jb-buttons>
        <jb-button type="submit" color="info" label="Login" />
        <!-- <jb-button to="/" color="info" outline label="Back" /> -->
      </jb-buttons>
    </card-component>
  </full-screen-section>
</template>

<script>
// import { reactive } from 'vue'
// import { useRouter } from 'vue-router'
import { mdiAccount, mdiAsterisk } from '@mdi/js'
import axios from 'axios'
import FullScreenSection from '@/components/FullScreenSection'
import CardComponent from '@/components/CardComponent'
import CheckRadioPicker from '@/components/CheckRadioPicker'
import Field from '@/components/Field'
import Control from '@/components/Control'
import Divider from '@/components/Divider.vue'
import JbButton from '@/components/JbButton'
import JbButtons from '@/components/JbButtons'
/* eslint-disable */

export default {
  name: 'Login',
  components: {
    FullScreenSection,
    CardComponent,
    CheckRadioPicker,
    Field,
    Control,
    Divider,
    JbButton,
    JbButtons
  },
  setup () {
    // const form = reactive({
    //   username: '',
    //   password: '',
    //   // remember: ['remember']
    // })

  //   const router = useRouter()

  //   const submit = () => {
  //     router.push('/')
  //   }

    return {
      mdiAccount,
      mdiAsterisk
    }
  },
  data () {
    return {
        username:'',
        password:'',
        errors: []
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
  methods: {
    async submit() {
        axios.defaults.headers.common["Authorization"] = ""

        localStorage.removeItem("token")

        const dataForm = {
            username: this.username,
            password: this.password
        };

        await axios
            .post("/api/v1/auth/login/", dataForm)
            .then(response => {
                const token = response.data.access_token
                // const user = response.data.user

                this.$store.commit('setToken', token)
                // this.$store.commit('setUser', user)

                axios.defaults.headers.common["Authorization"] = "Bearer " + token
                // axios.defaults.headers.common["Accept"] = "application/json"

                localStorage.setItem("token", token)

                const toPath = this.$route.query.to || '/dashboard'
                this.$router.push(toPath)
            })
            .catch(error => {
                if (error.response) {
                    // for (const property in error.response.data) {
                    //     this.errors.push(`${property}: ${error.response.data[property]}`)
                    // }
                    console.log(JSON.stringify(error.response.data))
                } else if (error.message) {
                    // this.errors.push('something wrong')
                    console.log(JSON.stringify(error))
                }
            })
      }
  }
}
</script>
