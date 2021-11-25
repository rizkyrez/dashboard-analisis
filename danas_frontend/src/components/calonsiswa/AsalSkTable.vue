<template>
    <!-- eslint-disable -->
  <div>
      <table class="border border-collapse border-green-400 text-xs w-full">
            <thead class="flex w-full">
                <tr class="bg-green-300 flex w-full">
                    <th class="font-semibold w-full">Asal Sekolah</th>
                    <th class="font-semibold w-1/3">Total</th>
                </tr>
            </thead>
            <tbody class="flex flex-col justify-between overflow-y-scroll w-full" style="height: 50vh">
                <tr v-for="ask in dataTable" :key="ask.name" class="flex w-full">
                    <td class="w-full">
                        {{ask.name}}
                    </td>
                    <td class="w-1/3 text-center">{{ask.value}}</td>
                </tr>
            </tbody>
            <tfoot class="flex w-full">
                <tr class="bg-gray-300 flex w-full">
                    <td class="font-semibold w-full" >
                        Grand Total
                    </td>
                    <td class="w-1/2 text-center">{{grandtotal}}</td>
                </tr>
            </tfoot>
        </table>
  </div>

</template>

<script>
/* eslint-disable */
import { computed, ref, watchEffect, toRefs, inject, watch  } from 'vue'
import { useStore } from 'vuex'
import Level from '@/components/Level'

export default {
  name: 'AsalSkTable',
  components: {
    Level,
  },
  props: {
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
