<template>
    <!-- eslint-disable -->
  <table class="border border-collapse  overflow-auto w-max-full border-green-400 text-xs">
    <thead class="flex w-full">
    <tr class="bg-green-300 flex w-full">
      <th v-if="cat == 'maker'" class="font-semibold flex w-full">Masa Kerja (Year)</th>
      <th v-else-if="cat == 'karyawan'" class="font-semibold flex w-full">Pegawai</th>
      <th v-else class="font-semibold flex w-full">Pendidikan</th>
      <th v-if="cat == 'karyawan'" class="flex w-full">Total Anak</th>
      <th v-else class="flex w-full">Total</th>
    </tr>
    </thead>
    <tbody v-if="cat == 'maker'" class="flex flex-col items-center justify-between overflow-y-scroll w-full scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600 hgtable">
        <tr v-for="pdk in dataTable" :key="pdk.masa_kerja" class="flex w-full">
          <td class="flex w-full">
            {{pdk.masa_kerja}}
          </td>
          <td class="flex w-full">{{pdk.value}}</td>
      </tr>
      <tr class="flex w-full">
          <td class="font-semibold flex w-full" >
            Total Pegawai
          </td>
          <td class="font-bold flex w-full">{{grandtotal}}</td>
      </tr>
    </tbody>

    <tbody v-else class="flex flex-col items-center justify-between">
      <tr v-for="pdk in dataTable" :key="pdk.name" class="flex w-full">
          <td class="flex w-full">
            {{pdk.name}}
          </td>
          <td class="flex w-full">{{pdk.value}}</td>
      </tr>
      <tr class="flex w-full">
          <td class="font-semibold flex w-full" >
            Total
          </td>
          <td class="font-bold flex w-full">{{grandtotal}}</td>
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

<style lang="postcss">
.hgtable {
    height: 50vh;
}
</style>
