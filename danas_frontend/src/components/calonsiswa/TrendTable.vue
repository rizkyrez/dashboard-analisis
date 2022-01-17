<template>
<!-- eslint-disable  -->
    <div class="overflow-auto scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600">
        <table class="border border-collapse overflow-auto w-max-full border-green-400 text-xs">
            <thead class="flex w-full">
                <tr class="bg-green-300 flex w-full">
                   <th class="font-semibold text-center flex w-full items-center" v-if="typedata == 'kelas'">Tingkat</th>
                   <th class="font-semibold text-center flex w-full items-center" v-else>Departemen</th>
                   <th v-for="hdr in header" class="font-semibold text-center flex w-full items-center"><span v-if="cat == 'siswa'">Tahun Ajaran <br> {{hdr}}</span><span v-if="cat == 'kelas'">Tahun Ajaran <br> {{hdr}}</span><span v-else-if="cat == 'calons'">{{hdr}}</span></th>
                   <th class="font-semibold text-center flex w-full items-center" v-if="cat == 'calons' && header.length > 1">Total Calon Siswa</th>
                   <th class="font-semibold text-center flex w-full items-center" v-else-if="cat == 'siswa'  && header.length > 1">Total Siswa</th>
                   <th class="font-semibold text-center flex w-full items-center" v-else-if="cat == 'kelas'  && header.length > 1">Total Kelas</th>
                </tr>
            </thead>
            <tbody v-if="typedata == 'kelas'" class="flex flex-col items-center justify-between overflow-y-scroll w-full scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200
    dark:scrollbar-track-gray-800 dark:scrollbar-thumb-gray-600" :class="(hgtable == 'on') ? 'hgtable' : ''">
                <tr v-if="typedata == 'kelas'" v-for="(rowData,y) in datatable" :key="rowData.tingkat" class="flex w-full">
                    <td class="flex w-full">{{rowData.tingkat}}</td>
                    <td v-for="detailTotal in rowData.total" :key="detailTotal" class="flex w-full">{{detailTotal}}</td>
                    <td class="flex w-full" v-if="header.length > 1">{{rowData.grandtotal}}</td>
                </tr>
                <tr class="flex w-full">
                    <td class="font-semibold flex w-full" v-if="cat == 'kelas'">
                        Total Kelas
                    </td>
                    <td v-for="stpros in sum" :key="stpros" class="flex w-full items-center">{{stpros}}</td>

                    <td v-if="header.length > 1" class="font-bold flex w-full items-center">
                        {{grdtotal}}
                    </td>
                </tr>
            </tbody>
            <tbody v-else  class="flex flex-col items-right">
                <tr v-if="typedata == 'trend'" v-for="(rowData,y) in datatable" :key="rowData.departemen" class="flex w-full">
                    <td class="flex w-full">{{rowData.departemen}}</td>
                    <td v-for="detailTotal in rowData.total" :key="detailTotal" class="flex w-full">{{detailTotal}}</td>
                    <td v-if="header.length > 1" class="flex w-full ">{{rowData.grandtotal}} </td>
                </tr>
                <tr class="flex w-full">
                    <td class="font-semibold flex w-full" v-if="cat == 'calons'">
                        Total Calon Siswa
                    </td>
                    <td class="font-semibold flex w-full" v-else-if="cat == 'siswa'">
                        Total Siswa
                    </td>
                    <td v-for="stpros in sum" :key="stpros" class="flex w-full items-center ">{{stpros}}</td>

                    <td v-if="header.length > 1" class="font-bold flex w-full items-center">
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
    typedata: String,
    grdtotal: Number,
    cat: String,
    hgtable: String
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
