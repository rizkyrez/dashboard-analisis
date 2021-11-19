<template>
  <section class="p-0">
    <card-component>

      <!-- <ul>
        <li
          v-for="(title, index) in titleStack"
          :key="index"
          class="title-stack-item inline-block pr-3 text-2xl text-gray-500 dark:text-gray-400 last:pr-0 last:font-black last:text-black
            dark:text-gray-100">
          {{ title }}
        </li>
      </ul> -->
      <div class="grid grid-cols-3 grid-flow-col auto-cols-max md:auto-cols-min gap-3">

        <div class="px-3">
        <field label="Departemen">
            <control :options="opsiDepartmen" v-model="departemen" @change="changeProses()" />
        </field>
        </div>

        <div class="px-3">
        <field label="Proses">
            <control :options="opsiProses" v-model="proses" :isdisable="valu"/>
        </field>
        </div>

        <div v-if="menutp === 'agama'" class="px-3">
        <field label="Agama">
            <control :options="opsiAgama" v-model="agama" />
        </field>
        </div>

        <div v-if="menutp === 'area'" class="px-3">
        <field label="Area">
            <control :options="opsiArea" v-model="area" />
        </field>
        </div>
<!--
        <div v-if="menutp === 'achievement'" class="px-3">
        <field label="Achievement">
            <control :options="opsiAchiement" v-model="achievement" />
        </field>
        </div> -->
        <div class="flex items-end ">
            <jb-button label="Filter" color="success" @click="clickedFilter()"></jb-button>
        </div>
    </div>

    </card-component>
  </section>
</template>

<script>
/* eslint-disable */
import { computed, ref, inject, watchEffect } from 'vue'
import axios from 'axios'
import Level from '@/components/Level'
import Field from '@/components/Field'
import Control from '@/components/Control'
import JbButton from '@/components/JbButton'
import CardComponent from '@/components/CardComponent'

export default {
  name: 'FilterBar',
  components: {
    Level,
    Field,
    JbButton,
    Control,
    CardComponent,

  },
  props: {
    titleStack: {
      type: Array,
      default: () => []
    },

    menutp: String
  },

  emits : ['changeProses','clickedFilter'],
  setup (props,{emit}) {
    const opsiDepartmen = inject('list_departemen')
    const opsiProses = inject('list_proses')
    const opsiAgama = inject('list_agama')
    const departemen = ref('All')
    const proses = ref('All')
    const agama = ref('All')

    const opsiArea = [
        { id: 1, label: 'Kosambi' },
        { id: 2, label: 'Green Lake City' },
        { id: 3, label: 'TSI' },
        { id: 4, label: 'Poris Indah' },
        { id: 5, label: 'Kosambi Baru' },
        { id: 6, label: 'Green Village' },
    ]

    watchEffect(() => {
        departemen.value,
        proses.value,
        agama.value
        if (proses.value == '' || proses.value == null) {
            proses.value = 'All'
        }

    })

    const changeProses = () => {
        emit('changeProses', departemen.value)
    }

    const valu = computed(() => {
        if(departemen.value == 'All') {
            return true
        }
    })


    const clickedFilter = () => {
        const filterData = {
            departemen: departemen.value,
            proses: proses.value,
            agama: agama.value
        }

        emit('clickedFilter', filterData)
    };

    return {
      opsiDepartmen,
      opsiAgama,
      opsiArea,
      opsiProses,
      changeProses,
      clickedFilter,
      departemen,
      proses,
      agama,
      valu
    }
  }
}
</script>

<style scoped>
/* li.title-stack-item:not(:last-child):after {
  content: '/';
  @apply inline-block pl-3;
} */
</style>
