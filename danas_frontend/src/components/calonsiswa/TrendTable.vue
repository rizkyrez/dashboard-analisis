<template>
<!-- eslint-disable  -->
    <div class="overflow-auto scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600">
        <table class="border border-collapse overflow-auto w-max-full border-green-400 text-xs">
            <thead>
                <tr class="bg-green-300">

                   <th class="font-semibold text-center">Departemen</th>
                   <th v-for="hdr in header" :key="hdr" class="font-semibold text-center"><span v-if="cat == 'siswa'">Tahun Ajaran </span>{{hdr}}</th>
                   <th class="font-semibold text-center" v-if="cat == 'calons'">Total Calon Siswa</th>
                   <th class="font-semibold text-center" v-if="cat == 'siswa'">Total Siswa</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rowData,y) in datatable" :key="rowData.departemen">
                    <td>{{rowData.departemen}}</td>
                    <td v-for="detailTotal in rowData.total" :key="detailTotal">{{detailTotal}}</td>
                    <td>{{rowData.grandtotal}}</td>
                </tr>
                <tr >
                    <td class="font-semibold" v-if="cat == 'calons'">
                        Total Calon Siswa
                    </td>
                    <td class="font-semibold" v-if="cat == 'siswa'">
                        Total Siswa
                    </td>
                    <td v-for="stpros in sum" :key="stpros">{{stpros}}</td>

                    <td class="font-bold">
                        {{grdtotal}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
/* eslint-disable */
import { computed, inject, provide, ref } from 'vue'
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
    cat: String
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
