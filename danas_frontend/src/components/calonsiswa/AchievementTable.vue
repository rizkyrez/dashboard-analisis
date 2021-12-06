<template>

  <table class="border border-collapse border-green-400 text-xs">
    <thead>
    <tr class="bg-green-300">
      <th v-for="title in header" :key="title" class="font-semibold">{{title}}</th>
      <!-- <th class="font-semibold">Gelombang TA 2022/2023</th> -->
      <th class="font-semibold">Total Calon Siswa</th>
      <th class="font-semibold">Achievement %</th>
    </tr>
    </thead>
    <tbody>
      <tr>
          <td v-for="dat in datatable" :key="dat">{{dat}}</td>
          <!-- <td>87</td> -->
          <td>{{grdtotal}}</td>
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
    grdtotal: Number
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
