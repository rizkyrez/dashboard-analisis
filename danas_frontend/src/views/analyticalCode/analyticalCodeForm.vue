<template>
  <hero-bar>Forms</hero-bar>
  <main-section>
    <card-component title="Analytical Code Form" :icon="mdiBallot" @submit.prevent="submit" form>
        <control type="hidden" v-model="form.id" id="id"/>
      <field label="Name">
        <control type="text" v-model="form.name" />
      </field>

      <divider/>

      <field label="Description" help="Your question. Max 255 characters">
        <control type="textarea" placeholder="Explain your description" v-model="form.description"/>
      </field>

      <divider/>

      <jb-buttons>
        <jb-button type="submit" color="info" label="Submit" @click="submit"/>
        <jb-button type="reset" color="info" outline label="Reset" />
      </jb-buttons>
    </card-component>
  </main-section>
  <bottom-other-pages-section />
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { mdiBallot, mdiBallotOutline, mdiAccount, mdiMail, mdiCheck } from '@mdi/js'
import MainSection from '@/components/MainSection'
import CardComponent from '@/components/CardComponent'
import HeroBar from '@/components/HeroBar'
import Field from '@/components/Field'
import Control from '@/components/Control'
import Divider from '@/components/Divider.vue'
import JbButton from '@/components/JbButton'
import JbButtons from '@/components/JbButtons'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'Forms',
  components: {
    Divider,
    MainSection,
    HeroBar,
    CardComponent,
    Field,
    Control,
    JbButton,
    JbButtons,
    BottomOtherPagesSection
  },
  setup () {
    const titleStack = ref(['Admin', 'Forms'])

    const form = reactive({
        id: '',
        name: '',
        description: ''
    })

    const errors = ref()

    const route = useRoute()
    const router = useRouter()

    const id = route.params.id

    const loadData = async () => {
        if (id) {
            await axios
            .get('/api/v1/analytical-code/' + id)
            .then(response => {
                if (response.data.success) {
                    console.log(response.data.data)
                    Object.assign(form, response.data.data)
                }
                // console.log(errors.value)
            })
            .catch(error => {
                if (error.response) {
                    console.log(error.response.data)
                    console.log(JSON.stringify(error.request))
                } else if (error.message) {
                    console.log(error.message)
                    console.log(JSON.stringify(error.message))
                }
            })
        }
    }

    const submit = async () => {
        const url = id ? '/api/v1/analytical-code/' + form.id : '/api/v1/analytical-code'
        const method = id ? 'PUT' : 'POST'
        await axios({
                method,
                url,
                params: form
            })
            .then(response => {
                if (response.data.success) {
                    console.log(response.data)
                } else {
                    errors.value = response.data.errorValidations
                }
                // console.log(errors.value)
            })
            .catch(error => {
                if (error.response) {
                    console.log(error.response.data)
                    console.log(JSON.stringify(error.request))
                } else if (error.message) {
                    console.log(error.message)
                    console.log(JSON.stringify(error.message))
                }
            })
    }

    onMounted(() => {
        // get API from Laravel Backend
        loadData()
    })

    return {
      titleStack,
      form,
      submit,
      mdiBallot,
      mdiBallotOutline,
      mdiAccount,
      mdiMail,
      mdiCheck,
      router
    }
  }
}
</script>
