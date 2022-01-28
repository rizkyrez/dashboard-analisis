<template>
  <!-- eslint-disable -->
  <hero-bar>Administrasi Monitoring</hero-bar>
  <filter-bar category="guru" @clicked-filter="filter"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2 lg:h-full md:h-full">

      <div class="lg:w-full sm:col-span-5 md:col-span-5 xl:col-span-5 sm:w-1/3 md:w-full lg:h-full md:h-full">
          <card-component title="Statistik" :icon="mdiChartBar" class="h-full">
            <figure class="w-full h-full">
              <v-chart
                :option="AdministrativeMonitoring"
              />
            </figure>
          </card-component>
      </div>
<!-- 
      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <Ads-Monitor-table />
        </card-component>
      </div> -->

    </div>

  </main-section>
</template>

<script>
/* eslint-disable */
import axios from 'axios'
import { ref, reactive, defineComponent, provide, onUpdated, onMounted, onBeforeMount, computed, watchEffect} from 'vue'
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart,LineChart } from 'echarts/charts';
import { UniversalTransition } from 'echarts/features';
import {
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
import AdsMonitorTable from '@/components/guru/AdsMonitorTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'

use([
  CanvasRenderer,
  BarChart,
  LineChart,
  UniversalTransition,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
]);

export default defineComponent({
  name: 'AdministrativeMonitoring',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    AdsMonitorTable,
    VChart
  },
  setup () {
    const store = useStore()
    const titleStack = ref(['Dashboard', 'Guru'])
    const dataset = ref([])
    const AdministrativeMonitoring = ref()


    const getData = async() => {
        const defaultDep = {
            departemen: 'SMA'
        }

        await axios
            .post('/api/v1/guru/administrative-monitor/', defaultDep)
            .then(res => {
                dataset.value = res.data.dataset
            })
            .catch(error => {
                console.log(JSON.stringify(error))
                alert(error.message)
            })
    }

    const max = computed(() => {
        var arrobj = dataset.value
        var data = arrobj.map(p => p.max)
        return data
    })

    const min = computed(() => {
        var arrobj = dataset.value
        var data = arrobj.map(p => p.min)
        return data
    })

    const avg = computed(() => {
        var arrobj = dataset.value
        var data = arrobj.map(p => p.avg)
        return data
    })

    const yaxis = computed(() => {
        var arrobj = dataset.value
        var data = arrobj.map(p => p.nipguru)
        return data
    })

    onMounted(() => {
        getData();
        AdministrativeMonitoring.value = {
            notmerge: true,
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                  type: 'cross'
                }
            },
            grid: {
                left: '13%',
                right: '8%',
                bottom: '7%',
                top:'15%',
                z: 4
            },
            legend: {
                left: 'center',
                bottom: 'Top',
                itemGap: 12,
                padding: 8,
                data: ['Max','Min','Avg']
            },
            xAxis: {
                name: 'Hari',
                type: 'value',
                alignTicks: true,
            },
            yAxis: {
              name: 'NIP',
              type: 'category',
              axisLabel: {
                  interval: 0,
                  formatter: '{value}',
                  align: 'center'
              },
              data: yaxis
            },
            series: [
                {
                    name: 'Max',
                    type: 'bar',
                    barGap: '-100%',
                    data: max,
                    itemStyle: {
                      color: '#1420a0',
                      opacity: 0.5
                    }
                },
                {
                    name: 'Min',
                    type: 'bar',
                    barGap: '-100%',
                    data: min,
                    itemStyle: {
                      color: '#2440b6',
                    }
                },
                {
                    name: 'Avg',
                    type: 'line',
                    data: avg,
                    itemStyle: {
                      color: '#EE6666'
                    }
                }
            ]
        }
    })


    watchEffect(() => {
        dataset.value
        AdministrativeMonitoring.value,
        max,
        min,
        avg,
        yaxis
    })

    const filter = async (e) => {
        const fil = {
            departemen: e.departemenGuru,        
        }

        await axios
            .post("/api/v1/guru/administrative-monitor/",fil)
            .then(res => {
                dataset.value = res.data.dataset
            })
            .catch(error =>{
                console.log(JSON.stringify(error))
                alert(error.message)
            })
    }

    return {
        titleStack,
        mdiTableBorder,
        mdiChartBar,
        mdiTableMultiple,
        dataset,
        AdministrativeMonitoring,
        filter
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
  height: auto;
  min-height: 600px;
  max-height: 1500px;
}
</style>
