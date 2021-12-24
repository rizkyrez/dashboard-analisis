<template>
<!-- eslint-disable  -->
    <div class="overflow-auto scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600">
        <table class="border border-collapse overflow-auto w-max-full border-green-400 text-xs">
            <thead class="flex w-full ">
                <tr class="bg-green-300 flex w-full">
                   <th class="font-semibold text-center flex w-full mx-4">No. KK</th>
                   <th v-for="hdr in header" class="font-semibold text-center flex w-full justify-between">{{hdr}}</th>
                   <th class="font-semibold flex w-full">Total</th>
                </tr>
            </thead>
            <tbody class="flex flex-col items-center justify-between overflow-y-scroll w-full scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600 hgtable">
                <tr v-for="(rowData,y) in datatable" :key="rowData.kk_no" class="flex w-full">
                    <td class="flex w-full">{{rowData.kk_no}}</td>
                    <td v-for="detailTotal in rowData.total" :key="detailTotal" class="flex w-full">{{detailTotal}}</td>
                    <td class="flex w-full">{{rowData.grandtotal}}</td>
                </tr>
                <tr class="flex w-full">
                    <td class="font-semibold flex w-full mx-5">
                        Total
                    </td>
                    <td v-for="stdp in sum" :key="stdp" class="flex w-full ml-4"><span v-for="st in stdp">{{st}}</span></td>

                    <td class="font-bold flex w-full items-center">
                        {{grdtotal}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
/* eslint-disable */
import { computed, inject, provide, ref, watchEffect } from 'vue'
import { useStore } from 'vuex'
import Level from '@/components/Level'
// import useTrendCs from '@/composables/useTrendcalonsiswa'

export default {
  name: 'TrendTable',
  components: {
    Level,
  },
  props: {
    header: Array,
    datatable: Array,
    sum: Array,
    grdtotal: Number,
  },
  setup (props) {
    const store = useStore()
    const darkMode = computed(() => store.state.darkMode)
    // const header = inject('header')
    // const sum = inject('sum')
    // const datatable = inject('datatable')
    // const grdtotal = inject('grdtotal')
    // const { header, datatable, sum, grdtotal } = useTrendCs()

    return {
      darkMode,
    //   header,
    //   datatable,
    //   sum,
    //   grdtotal
    }
  },

}
</script>
<style lang="postcss">
.hgtable {
    height: 50vh
}
</style>
