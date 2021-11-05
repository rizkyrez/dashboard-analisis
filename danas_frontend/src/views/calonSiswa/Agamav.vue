
<template>

  <hero-bar>Agama</hero-bar>
  <filter-bar menutp="agama"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart
                :option="agama"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <agama-table/>
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
import { LabelLayout } from 'echarts/features';
import { PieChart } from 'echarts/charts';
import {
  TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
} from 'echarts/components';
import VChart, { THEME_KEY } from 'vue-echarts';
import { ref, defineComponent } from 'vue'
import {  mdiTableBorder, mdiTableOff, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import AgamaTable from '@/components/calonsiswa/AgamaTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'

use([
  CanvasRenderer,
  PieChart,
  LabelLayout,
  TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
]);


export default defineComponent({
  name: 'Agama',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    AgamaTable,
    VChart

  },
  setup () {
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const agama = ref({
      title: {
        text: "Total Agama",
        left: "center",
      },
      tooltip: {
        trigger: "item",
        // formatter: "{a} <br/>{b} : {c} ({d}%)"
      },
      legend: {
        orient: 'horizontal',
        left: 'center',
        top: 70
      },
      dataset: {
        source: [
            ['Agama', 'jumlah'],
            ['Buddha', 41],
            ['Islam', 1],
            ['Katolik', 9],
            ['Kong Hu Cu', 1],
            ['Kristen', 3],
            ['NULL', 10]
        ]
      },
      series: [
        {
            type: "pie",
            radius: "75%",
            label: {
                position: 'outer',
                alignTo: 'labelLine',
                bleedMargin: 6
            },
            emphasis: {
                itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            top: 100
        },

      ]
    })

    return {
      titleStack,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      agama
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
  min-height: 350px;
  max-height: 100%;
}
</style>
