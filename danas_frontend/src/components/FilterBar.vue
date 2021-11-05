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
        <field label="Proses">
            <control :options="opsiTahunajaran" v-model="tahunajaran" />
        </field>
        </div>

        <div class="px-3">
        <field label="Departemen">
            <control :options="opsiDepartmen" v-model="departemen" />
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
            <jb-button label="Filter" color="success" @click.prevent="filter()"></jb-button>
        </div>
    </div>

    </card-component>
  </section>
</template>

<script>
/* eslint-disable */
import { mdiGithub } from '@mdi/js'
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
  setup () {
    const opsiDepartmen = [
      { id: 1, label: 'TK' },
      { id: 2, label: 'SD' },
      { id: 3, label: 'SMP' },
      { id: 4, label: 'SMA' }
    ]

    const opsiTahunajaran = [
      { id: 1, label: 'TA 21/22' },
      { id: 2, label: 'TA 22/23' },
    ]

    const opsiAgama = [
      { id: 1, label: 'Buddha' },
      { id: 2, label: 'Islam' },
      { id: 3, label: 'Kong Hu Cu' },
      { id: 4, label: 'Kristen' }
    ]

    const opsiArea = [
      { id: 1, label: 'Kosambi' },
      { id: 2, label: 'Green Lake City' },
      { id: 3, label: 'TSI' },
      { id: 4, label: 'Poris Indah' },
      { id: 5, label: 'Kosambi Baru' },
      { id: 6, label: 'Green Village' },
    ]

    return {
      opsiDepartmen,
      opsiTahunajaran,
      opsiAgama,
      opsiArea
      // mdiGithub
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
