
<template>

  <hero-bar>Achievement</hero-bar>
  <filter-bar category="calons" menutp="achie" :listop="header"/>
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
          <achievement-table :header="header" :datatable="datatable" :grdtotal="grdtotal" :percentage="valu"/>
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
import { GaugeChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
} from 'echarts/components';
import VChart, { THEME_KEY } from 'vue-echarts';
import { ref, defineComponent, reactive, computed, watch, watchEffect, onBeforeMount, onMounted } from 'vue'
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
    const datatable = ref([])
    const header = ref([])
    const grdtotal = ref()
    const valu = ref()

    // const percentage = computed(() => {
    //     const arrobj = datatable.value

    //     let arrdata = arrobj.map(p => p.array_data)

    //     let result = arrdata.reduce((a, v) => {
    //         return a += v
    //     },0)

    //     return result
    // })

    const achievement = reactive({
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
                min: 0,
                max: 200,
                splitNumber: 10,
                axisLine: {
                    lineStyle: {
                        width: 7,
                        color: [
                            [0.25, '#ee4c5d'],
                            [0.5, '#ecef55'],
                            [1, '#55db80'],
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
                    formatter: '{value}%'
                },
                data: [
                    {
                    value: valu,
                    name: 'Achievement'
                    }
                ],
            }
        ]
    })

    onMounted(async () => {
        await axios
            .get('/api/v1/calons/achievement')
            .then(res => {
                datatable.value = res.data.data_table
                header.value = res.data.header
                grdtotal.value = res.data.grdtotal
                valu.value = res.data.percentage
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error))
            })
    })

    watchEffect(() => {
        datatable.value,
        header.value,
        grdtotal.value,
        valu.value,
        achievement
    })

    return {
      titleStack,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      datatable,
      valu,
      datatable,
      grdtotal,
      header,
      achievement
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
  min-height: 350px;
  max-height: 100%;
}
</style>
