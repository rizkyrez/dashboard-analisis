
<template>

  <hero-bar>Achievement</hero-bar>
  <filter-bar/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart
                :option="achievement"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <achievement-table/>
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
import { GaugeChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
} from 'echarts/components';
import VChart, { THEME_KEY } from 'vue-echarts';
import { ref, defineComponent } from 'vue'
import {  mdiTableBorder, mdiTableOff, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import AchievementTable from '@/components/calonsiswa/AchievementTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'

use([
  CanvasRenderer,
  GaugeChart,
  TitleComponent,
  TooltipComponent,
]);


export default defineComponent({
  name: 'Achievement',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    AchievementTable,
    VChart

  },
  setup () {
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const achievement = ref({
      title: {
        text: "Speedometer",
        left: "center",
      },
      tooltip: {
        formatter: '{b} : {c}%',
      },
      series: [
          {
            type: 'gauge',
            radius: '100%',
            center: ["50%","70%"],
            startAngle: 180,
            endAngle: 0,
            splitNumber: 10,
            axisLine: {
                lineStyle: {
                width: 7,
                color: [
                    [0.25, '#FF5e6f'],
                    [0.5, '#f9ee55'],
                    [1, '#58EC90'],
                ]
                }
            },
            pointer: {
                width: 5,
                itemStyle: {
                color: 'auto'
                }
            },
            axisTick: {
                length: 5,
                lineStyle: {
                color: 'auto',
                width: 1
                }
            },
            splitLine: {
                length: 10,
                lineStyle: {
                color: 'auto',
                width: 2
                }
            },
            axisLabel: {
                color: 'auto',
                fontSize: 12,
                distance: 10,
            },
            title: {
                fontSize: 15
            },
            detail: {
                fontSize: 17,
                valueAnimation: true,
                formatter: '{d}%'
            },
            data: [
                {
                value: 83.7,
                name: 'Achievement'
                }
            ],
        }
      ]
    })

    return {
      titleStack,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      achievement
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
