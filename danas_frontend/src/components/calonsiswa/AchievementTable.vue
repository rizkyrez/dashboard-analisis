<template>

  <table class="border border-collapse border-green-400 text-xs">
    <thead>
    <tr class="bg-green-300">
      <!-- <th v-for="title in header" :key="title" class="font-semibold">{{title}}</th> -->
      <!-- <th class="font-semibold">Gelombang TA 2022/2023</th> -->
      <!-- <th class="font-semibold">Total Calon Siswa</th>
      <th class="font-semibold">Achievement %</th> -->
    </tr>
    </thead>
    <tbody>
      <template v-for="data in datatable" :key="data">
        <tr class="bg-green-300">
          <th >Departemen</th>
          <th v-for="dtl in data.header" :key="dtl">{{dtl}}</th>
          <th class="font-semibold">Achievement %</th>
        </tr>
        <tr>
          <td >{{data.departemen}}</td>
          <td v-for="val in data.data_table" :key="val">{{val}}</td>
        </tr>
      </template>
      <!-- <tr>
          <td v-for="data in datatable" :key="data">{{data}}</td>
          <td>{{grdtotal}}</td>
          <td>{{percentage}}%</td>
      </tr> -->
      <!-- <template v-for="data in dataTable" :key="data">
          <tr >
            <td>{{data}} asd </td>
          </tr>
      </template> -->
        <tr class="bg-green-300">
          <td>Grand total</td>
          <td v-for="x in grandtotal" :key="x">{{x}}</td>
          <td>{{percentage}}%</td>
        </tr>
    </tbody>
  </table>

</template>

<script>
/* eslint-disable */
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import Level from '@/components/Level'

export default {
  name: 'AchievementTable',
  components: {
    Level,
  },
  props: {
    checkable: Boolean,
    datatable: Array,
    header: Array,
    percentage: Number,
    grandtotal: Array
  },
  setup () {
    const store = useStore()

    const darkMode = computed(() => store.state.darkMode)

    return {
      darkMode,
    }
  }
}
</script>
