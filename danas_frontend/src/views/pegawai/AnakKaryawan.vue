<template>
    <!-- eslint-disable -->
  <hero-bar>Jumlah Anak Karyawan</hero-bar>
  <filter-bar category="pegawai" menutp="pendidikan" @clicked-filter="filter"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart
                :option="akpegawai"
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <pendidikan-table cat="karyawan"/>
        </card-component>
      </div>

    </div>

  </main-section>
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
import PendidikanTable from '@/components/pegawai/PendidikanTable'
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
  name: 'AnakKaryawan',
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
  setup (props) {
    const titleStack = ref(['Dashboard', 'Pegawai'])
    const akpegawai = ref()
    const akpdata = ref([])

    //  const legendat = computed(() => {
    //     let arobj = akpdata.value

    //     let prop = arobj.map(p => p.name)

    //     return prop
    // })

    onMounted(async() => {
        await axios
            .get('/api/v1/kepegawaian/keluargapegawai/')
            .then(res => {
                akpdata.value = res.data.dataset
            })
            .catch(error => {
                alert(error.message)
                console.log("somthieng wrong")
                console.log(JSON.stringify(error))
            })

        akpegawai.value = {
            title: {
                text: "Total Anak Guru/Karyawan",
                left: "center",
            },
            tooltip: {
                trigger: "item",
                formatter: "{b} : {c}"
            },
            legend: {
                orient: 'horizontal',
                left: 'center',
                top: 50
            },

            series: [
                {
                    type: "pie",
                    radius: "70%",
                    label: {
                        position: 'outer',
                        alignTo: 'labelLine',
                        minMargin: 3,
                        distanceToLabelLine: 5,
                        formatter: '{b} \n {c}',
                        bleedMargin: 6
                    },
                    labelLine: {
                        length: 25,
                        length2: 15
                    },
                    data: akpdata,
                    emphasis: {
                        itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    },
                    top: 95
                },

            ]
        }
    })

    watchEffect(() => {
        akpdata.value,
        akpegawai
    });

    // const filter = async (e) => {
    //     if(e.tahunajaran == 'All' || e.tahunajaran == null || e.tahunajaran == '') {
    //         e.tahunajaran = ''
    //     }

    //     if(e.departemen == 'All') {
    //         e.departemen = ''
    //         e.tahunajaran = ''
    //     }

    //     if(e.agama == 'All') {
    //         e.agama = ''
    //     }

    //     const fil = {
    //         departmen: e.departemen,
    //         tahunajaran: e.tahunajaran,
    //         agama: e.agama
    //     }

    //     await axios
    //         .post("/api/v1/kepegawaian/filterag/", fil)
    //         .then(res => {
    //             agdata.value = res.data.dataset
    //         })
    //         .catch(error =>{
    //             console.log('something wrong')
    //             console.log(JSON.stringify(error))
    //         })
    // }

    provide('dataTable', akpdata)

    return {
      titleStack,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      akpdata,
      akpegawai,
    //   filter,
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
  height: 360px;
  min-height: 360px;
  max-height: 100%;
}
</style>
