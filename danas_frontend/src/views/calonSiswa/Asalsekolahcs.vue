
<template>

  <hero-bar>Agama</hero-bar>
  <filter-bar @change-proses="changepros" @clicked-filter="filter"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart
                :option="asalSekolah"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <asal-sekolah-table/>
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
import { ref, defineComponent, computed, watchEffect, onBeforeUpdate, onBeforeMount, onMounted, provide, nextTick } from 'vue'
import axios from 'axios'
import {  mdiTableBorder, mdiTableOff, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import AsalSekolahTable from '@/components/calonsiswa/AsalSkTable'
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
  name: 'Asalsekolah',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    AsalSekolahTable,
    VChart

  },
  setup (props) {
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const asalSekolah = ref()
    const askdata = ref([])
    const listDep = ref([])
    const listProp = ref([])
    const listAsk = ref([])

     const legendat = computed(() => {
        let arobj = askdata.value

        let prop = arobj.map(p => p.name)

        return prop
    })

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

    onMounted(async() => {
        await axios
            .get('/api/v1/calons/asalsekolah/')
            .then(res => {
                askdata.value = res.data.askdata
                listDep.value = res.data.list_departemen
                listProp.value = res.data.list_proses
                listAsk.value = res.data.list_asalsekolah
            })
            .catch(error => {
                if (error.message) {
                    console.log("somthieng wrong")
                    console.log(JSON.stringify(error))
                }
            })

        asalSekolah.value = {
            title: {
                text: "Total Agama",
                left: "center",
            },
            tooltip: {
                trigger: "item",
                // formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                type: 'scroll',
                orient: 'horizontal',
                right: 10,
                bottom: -5,
                itemHeight: 12,
                height: 100
            },
            series: [
                {
                    type: "pie",
                    radius: "55%",
                    label: {
                        position: 'outer',
                        alignTo: 'labelLine',
                        bleedMargin: 6
                    },
                    data: askdata,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 15,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 5, 0, 0.9)'
                        }
                    },
                    top: 30,
                    bottom: 20
                },

            ]
        }
    })

    watchEffect(() => {
        listDep.value,
        listProp.value,
        listAsk.value,
        askdata.value,
        legendat,
        asalSekolah
    });

    const filter = (e) => {
        if(e.proses == 'All' || e.proses == null || e.proses == '') {
            e.proses = ''
        }

        if(e.departemen == 'All') {
            e.departemen = ''
            e.proses = ''
        }

        // if(e.ask == 'All') {
        //     e.ask = ''
        // }

        const fil = {
            departmen: e.departemen,
            proses: e.proses,
        }

        axios
            .post("/api/v1/calons/filterask/", fil)
            .then(res => {
                askdata.value = res.data.askdata
            })
            .catch(error =>{
                console.log('something wrong')
                console.log(JSON.stringify(error))
            })
    }

    provide('dataTable', askdata)
    provide('list_departemen', listDep)
    provide('list_proses', listProp)
    provide('list_asalsekolah',listAsk)

    return {
      titleStack,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      askdata,
      asalSekolah,
      filter,
      changepros
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
  min-height: 550px;
  max-height: 100%;
}
</style>
