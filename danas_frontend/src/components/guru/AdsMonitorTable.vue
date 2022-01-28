<template>
    <!-- eslint-disable -->
  <table class="border border-collapse  overflow-auto w-max-full border-green-400 text-xs">
    <thead class="flex w-full">
    <tr class="bg-green-300 flex w-full">
      <th class="font-semibold flex w-full">nip</th>
      <th class="font-semibold flex w-full">nama</th>
      <th class="font-semibold flex w-full">average</th>
    </tr>
    </thead>
    <tbody class="flex flex-col items-center justify-between overflow-y-scroll w-full scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600 hgtable">
        <tr v-for="pdk in dataTable" :key="pdk" class="flex w-full">
          <td class="flex w-full">
            {{pdk.nip}}
          </td>
          <td class="flex w-full">
            {{pdk.name}}
          </td>
          <td class="flex w-full">{{pdk.average}}</td>
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
