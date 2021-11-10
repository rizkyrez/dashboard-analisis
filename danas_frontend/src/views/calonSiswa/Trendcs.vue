
<template>

  <hero-bar>Trend Jumlah Calon Siswa</hero-bar>
  <filter-bar/>
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
import axios from 'axios'
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart } from 'echarts/charts';
import {
  // TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
} from 'echarts/components';
import VChart, { THEME_KEY } from 'vue-echarts';
import { ref, defineComponent, watch, computed, onMounted, beforeCreate } from 'vue'
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
  setup () {
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const dataset = ref([])
    const trendcs = ref()

    onMounted(async () =>  {
        await axios.get('/api/v1/calons/')
        .then(res => {
            dataset.value = res.data.dataset
        })
        .catch(error =>{
            if (error.response) {
                for (const property in error.response.data) {
                    this.errors.push(`${property}: ${error.response.data[property]}`)
                }
                console.log(JSON.stringify(error.response.data))
            } else if (error.message) {
                this.errors.push('something wrong')
                console.log(JSON.stringify(error))
            }
        })
        trendcs.value = {
            aria: {
                show: true
            },
            tooltip: {
                trigger: 'axis',
                axisPointer:{
                type: 'shadow',
                label: {
                    show: true
                }
                }
            },
            grid: {
                left: '6%',
                right: '6%',
                bottom: '6%',
                top:80,
                z: 4
            },
            legend: {
                orient: 'horizontal',
                left: 'center',
                top: 'top',
                itemGap: 12,
                padding: 8
                // top: 'center'
            },
            xAxis: {type: 'category'},
            yAxis: {type: 'value'},
            series: dataset
        };

        const dataseries =  computed({
            get: () => dataset.value,
            set: val => {
                dataset.value = val
            }
        });

        watch(dataseries, dataset => {
            if (trendcs) {
                trendcs.series = d
                trendcs.update()
            }
        });
    });

    return {
      titleStack,
      mdiMonitorCellphone,
      mdiAccountMultiple,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      trendcs
    }
  },

//   watch: {
//      arrData: function () {
//        this.getChartData()
//     },
//   },
//   methods: {
//      async getChartData () {
//       await axios
//         .get(`/api/v1/calons/`)
//         .then(response => {
//           dataset.value = response.data.data
//         })
//         .catch(error => {
//             this.errors.push('something wrong')
//             console.log(JSON.stringify(error))
//         })
//     }
//   },

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
