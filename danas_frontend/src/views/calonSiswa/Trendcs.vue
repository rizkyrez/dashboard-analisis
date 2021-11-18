
<template>

  <hero-bar>Trend Jumlah Calon Siswa</hero-bar>
  <filter-bar @change-proses="changepros" @clicked-filter="filter"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart
                :option="trendcs"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <trend-table />
        </card-component>
      </div>

    </div>

  </main-section>

  <bottom-other-pages-section />
</template>

<script>
/* eslint-disable */
import { ref, defineComponent, computed, watchEffect, onBeforeUpdate, onBeforeMount, onMounted, provide, nextTick} from 'vue'
import axios from 'axios'
import _ from 'lodash'
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
import { useStore } from 'vuex'
import VChart, { THEME_KEY } from 'vue-echarts';
import { mdiMonitorCellphone, mdiAccountMultiple, mdiTableBorder, mdiTableOff, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import TrendTable from '@/components/calonsiswa/TrendTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'

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
  name: 'TrendCalonSiswa',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    TrendTable,
    VChart
  },
  props: {
      key: String,
  },
  setup (props) {
    const store = useStore()
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const dataset = ref([])
    const dataTable = ref([])
    const trendcs = ref()
    const sum = ref([])
    const listDep = ref([])
    const listProp = ref([])
    const header = ref([])
    const vpros = ref()

    onMounted(async () => {

        await axios
            .get('/api/v1/calons/')
            .then(res => {
                dataset.value = res.data.dataset
                dataTable.value = res.data.data_table
                sum.value = res.data.sum_pros
                listDep.value = res.data.list_departemen
                listProp.value = res.data.list_proses
                header.value = res.data.header
            })
            .catch(error =>{
                if (error.message) {
                    this.errors.push('something wrong')
                    console.log(JSON.stringify(error))
                }
            })


        trendcs.value = {
            notMerge:  true,
            tooltip: tooltip,
            grid: {
                left: '6%',
                right: '6%',
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
                data: legendat,
                // selectedMode: selectmode,
                // selected: selected,
                // top: 'center'
            },
            xAxis: {
                type: 'category',
                boundaryGap: true,
                data: xaxis,
                axisTick: {
                    alignWithLabel: true
                }
            },
            yAxis: {type: 'value'},
            series: dataset
        };
    })

    const xaxis = computed(() => {
        let arrobj = dataTable.value

        let dep = arrobj.map(p => p.departemen)

        return dep
    })

    const legendat = computed(() => {
        let arobj = dataset.value

        let prop = arobj.map(p => p.name)

        return prop
    })
    // const selectmode = computed(() => {
    //     if(xaxis.value.length == 1) {
    //         return 'single'
    //     } else {
    //         return 'multiple'
    //     }
    // })

    const tooltip = computed(() => {
        let lenxaxis = xaxis.value.length

        if(lenxaxis == 1) {
            const atr = {
                trigger: 'item'
            }
            return atr
        }
        else {
            const atr = {
               trigger: 'axis',
                axisPointer:{
                    type: 'shadow',
                        label: {
                            show: true
                        }
                }
            }

            return atr
        }
    })

    // const selected = computed(() => {
    //     const vps = vpros.value
    //     const listpros = legendat.value

    //     const obj = listpros.reduce(function (a, v) {
    //         if(v in a) {
    //             a[v]++
    //         } else {
    //             a[v] = true
    //         }

    //         return a
    //     }, {})

    //     if (obj[vps]) {
    //         obj[vps] = treue
    //     }
    //     return obj
    // })

    const changepros = async (event) => {
        await axios
            .post("/api/v1/calons/change/", {departmen: event})
            .then(res => {
                listProp.value = res.data.list_proses
            })
            .catch(error =>{
                    console.log('something wrong')
                    console.log(JSON.stringify(error))
            })
    }

    const filter = async (e) => {
        if(e.proses == 'All' || e.proses == null || e.proses == '') {
            e.proses = ''
        }

        if(e.departemen == 'All') {
            e.departemen = ''
            e.proses = ''
        }

        const fil = {
            departmen: e.departemen,
            proses: e.proses
        }

        await axios
            .post("/api/v1/calons/filter/", fil)
            .then(res => {
                dataset.value = res.data.dataset
                dataTable.value = res.data.data_table
                sum.value = res.data.sum_pros
                header.value = res.data.header
            })
            .catch(error =>{
                console.log('something wrong')
                console.log(JSON.stringify(error))
            })
    }

    watchEffect(() => {
        dataTable.value,
        listProp.value,
        sum.value,
        dataset.value,
        trendcs.value,
        header.value
        legendat,
        xaxis,
        tooltip
    });

    provide('arrdata', dataset)
    provide('dataTable', dataTable)
    provide('sum_proses', sum)
    provide('list_departemen', listDep)
    provide('header', header)
    provide('list_proses', listProp)

    return {
      titleStack,
      mdiMonitorCellphone,
      mdiAccountMultiple,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      trendcs,
      dataTable,
      sum,
      dataset,
      changepros,
      filter,
    }
  },

});
</script>
<style>
/**
 * The default size is 600px×400px, for responsive charts
 * you may need to set percentage values as follows (also
 * don't forget to provide a size for the container).
 */
.echarts {
  width: 100%;
  height: 400px;
}
</style>
