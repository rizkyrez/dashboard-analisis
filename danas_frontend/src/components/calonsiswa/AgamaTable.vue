<template>
    <!-- eslint-disable -->
  <table class="border border-collapse border-green-400 text-xs">
    <thead>
    <tr class="bg-green-300">
      <th class="font-semibold">Agama</th>
      <th v-if="cat == 'siswa'" class="font-semibold">Total Siswa</th>
      <th v-else class="font-semibold">Total Calon Siswa</th>
    </tr>
    </thead>
    <tbody>
      <tr v-for="agm in dataTable" :key="agm.name">
          <td >
            {{agm.name}}
          </td>
          <td>{{agm.value}}</td>
      </tr>
      <tr>
          <td v-if="cat == 'siswa'" class="font-semibold" >
            Total Siswa
          </td>
          <td v-else class="font-semibold" >
            Total Calon Siswa
          </td>
          <td class="font-bold">{{grandtotal}}</td>
      </tr>
    </tbody>
  </table>

</template>


<script>
/* eslint-disable */
import { computed, ref, watchEffect, toRefs, inject, watch  } from 'vue'
import { useStore } from 'vuex'
import Level from '@/components/Level'

export default {
  name: 'AgamaTable',
  components: {
    Level,
  },
  props: {
    cat: String,
    checkable: Boolean
  },
  setup () {
    const store = useStore()
    const darkMode = computed(() => store.state.darkMode)
    const dataTable = inject('dataTable')

    const grandtotal = computed(() => {
        const arr = dataTable.value

        return arr.reduce((acc, curV) => {
            return acc += curV.value
        },0)
    })

    watchEffect(() => {
        grandtotal,
        dataTable.value
    })

    return {
      darkMode,
      dataTable,
      grandtotal
    }
  }
}
</script>
