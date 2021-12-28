<template>
  <!-- eslint-disable -->
  <hero-bar>Masa Kerja Pegawai</hero-bar>
  <!-- <filter-bar category="calons" @clicked-filter="filter"/> -->
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-3 sm:w-1/3 md:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure class="w-full">
              <v-chart
                :option="workperiod"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <pendidikan-table cat="maker"/>
        </card-component>
      </div>

    </div>

  </main-section>
</template>

<script>
/* eslint-disable */
import axios from 'axios'
import { ref, reactive, defineComponent, provide, onUpdated, onMounted, onBeforeMount, computed, watchEffect} from 'vue'
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart } from 'echarts/charts';
import {
  // TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';
import { useStore } from 'vuex'
import { mdiTableBorder, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import PendidikanTable from '@/components/pegawai/PendidikanTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import useTrendCs from '@/composables/useTrendcalonsiswa'
import useFilter from '@/composables/useFilter'

use([
  CanvasRenderer,
  BarChart,
  // TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
]);


export default defineComponent({
  name: 'MasaKerjaPegawai',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    PendidikanTable,
    VChart
  },
  setup () {
    const store = useStore()
    const titleStack = ref(['Dashboard', 'Pegawai'])
    const dataset = ref([])
    const datatable = ref([])
    const header = ref([])
    const sum = ref([])

    const getData = async() => {
        await axios
            .get('/api/v1/kepegawaian/masakerja/')
            .then(res => {
                dataset.value = res.data.dataset
            })
            .catch(error => {
                console.log(JSON.stringify(error))
                alert(error.message)
            })
    }

    const workperiod = reactive({
        notMerge:  true,
        tooltip: {
            trigger: 'item'
        },
        grid: {
            left: '6%',
            right: '10%',
            bottom: '20%',
            top:30,
            z: 4
        },
        legend: {
            orient: 'horizontal',
            left: 'center',
            bottom: 'bottom',
            itemGap: 12,
            padding: 8,
        },
        xAxis: {
            name: 'Year',
            type: 'category',
            data: [],
            boundaryGap: true,
            axisLabel: {
                interval: 0,
                formatter: '{value}',
                align: 'center'
            },
        },
        yAxis: {type: 'value'},
        series: [
            {
                name: 'Masa Kerja',
                type: 'bar',
                data: []
            }
        ]
    })

    onMounted(() => {
        getData()
    })

    // const xaxis = computed(() => {
    //     let arrobj = datatable.value

    //     let dep = arrobj.map(p => p.departemen)

    //     return dep
    // })

    // const legendat = computed(() => {
    //     let arobj = dataset.value

    //     let prop = arobj.map(p => p.name)

    //     return prop
    // })

    // const tooltip = computed(() => {
    //     let lenxaxis = xaxis.value.length

    //     if(lenxaxis == 1) {
    //         const atr = {
    //             trigger: 'item'
    //         }
    //         return atr
    //     }
    //     else {
    //         const atr = {
    //            trigger: 'axis',
    //             axisPointer:{
    //                 type: 'shadow',
    //                     label: {
    //                         show: true
    //                     }
    //             }
    //         }

    //         return atr
    //     }
    // })

    // const grdtotal = computed(() => {
    //     const prosTotal = sum.value

    //     let sumpros = prosTotal.reduce((a, v) => {
    //         return a += v
    //     },0)

    //     return sumpros
    // })

    watchEffect(() => {
        dataset.value
        workperiod.xAxis.data = dataset.value.map(p => p.masa_kerja)
        workperiod.series[0].data = dataset.value.map(p => p.value)
        workperiod
        // header.value,
        // datatable.value,p
        // sum.value,
        // tooltip,
        // legendat,
        // xaxis,
        // grdtotal
    })

    provide('dataTable', dataset)

    // const filter = async (e) => {
    //     if(e.proses == 'All' || e.proses == null || e.proses == '') {
    //         e.proses = ''
    //     }

    //     if(e.departemen == 'All') {
    //         e.departemen = ''
    //         e.proses = ''
    //     }

    //     const fil = {
    //         departmen: e.departemen,
    //         proses: e.proses
    //     }

    //     await axios
    //         .post("/api/v1/calons/filter/", fil)
    //         .then(res => {
    //             dataset.value = res.data.dataset
    //             datatable.value = res.data.data_table
    //             sum.value = res.data.sum_pros
    //             header.value = res.data.header
    //         })
    //         .catch(error =>{
    //             console.log(JSON.stringify(error))
    //             alert(error.message)
    //         })
    // }

    return {
        titleStack,
        mdiTableBorder,
        mdiChartBar,
        mdiTableMultiple,
        dataset,
        workperiod,
        // datatable,
        // header,
        // sum,
        // grdtotal,
        // filter
    }
  },

});
</script>
<style lang="postcss">
html {
    scroll-behavior: smooth;
}

.echarts {
  width: 100%;
  height: 400px;
}
</style>
