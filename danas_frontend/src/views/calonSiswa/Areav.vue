
<template>

  <hero-bar>Area</hero-bar>
  <filter-bar menutp="area"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart
                :option="area"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <area-table/>
        </card-component>
      </div>

    </div>

  </main-section>

  <bottom-other-pages-section />
</template>

<script>
/* eslint-disable */
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
import { ref, defineComponent } from 'vue'
import {  mdiTableBorder, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import AreaTable from '@/components/calonsiswa/AreaTable'
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
  name: 'Areacs',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    AreaTable,
    VChart

  },
  setup () {
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const area = ref({
      aria: {
        show: true
      },
      // title: {
      //   text: "Trend Jumlah Calon Siswa",
      //   left: "center"
      // },
      tooltip: {
        trigger: 'axis',
        axisPointer:{
          type: 'shadow',
          label: {
            show: true
          }
        }
      },
      calculable: true,
      legend: {
        orient: 'horizontal',
        left: 'center',
        top: 'top'
      },
      dataset: {
        source: [
            ['Area', 'Gelombang TA 2021/2022', 'Gelombang TA 2022/2023'],
            ['Blank', 21, 11],
            ['Green Lake City', 6, 14],
            ['Kosambi Baru', 4, 12],
            ['Taman Semanan Indah', 2, 7],
            ['metro Permata', 1, 3],
            ['Metland', 0, 2],
            ['Kresek Indah', 1, 1],
            ['Green Lake Village', 0,1],
            ['Citra Garden', 0, 1],
            ['Green palm', 0, 1],
            ['Poris Paradise', 1, 0]
        ]
      },
      xAxis: {
          type: 'category',
        //    axisLabel: { interval: 0, rotate: 20 }
      },
      yAxis: {type: 'value'},
      series: [
        {
          type: "bar",
          color: '#33EE55',
        },
        {
          type: "bar",
          color: '#ef6622'
        },
      ]
    })

    return {
      titleStack,
      mdiTableBorder,
      mdiChartBar,
      mdiTableMultiple,
      area
    }
  },
  // data () {

  //   let data = []
  //   for (let i = 0; i <= 360; i++) {
  //       let t = i / 180 * Math.PI
  //       let r = Math.sin(2 * t) * Math.cos(2 * t)
  //       data.push([r, i])
  //   }

  //   return {
  //     initOptions: {
  //       renderer: 'canvas'
  //     },
  //     polar: {
  //       title: {
  //         text: '极坐标双数值轴'
  //       },
  //       legend: {
  //         data: ['line']
  //       },
  //       polar: {
  //         center: ['50%', '54%']
  //       },
  //       tooltip: {
  //         trigger: 'axis',
  //         axisPointer: {
  //           type: 'cross'
  //         }
  //       },
  //       angleAxis: {
  //         type: 'value',
  //         startAngle: 0
  //       },
  //       radiusAxis: {
  //         min: 0
  //       },
  //       series: [
  //         {
  //           coordinateSystem: 'polar',
  //           name: 'line',
  //           type: 'line',
  //           showSymbol: false,
  //           data: data
  //         }
  //       ],
  //       animationDuration: 2000
  //     }
  //   }
  // }
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
  min-height: 380px;
  max-height: 100%;
  padding-bottom: 20px;;
}
</style>
