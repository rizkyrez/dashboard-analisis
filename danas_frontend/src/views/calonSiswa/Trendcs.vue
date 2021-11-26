<template>
  <!-- eslint-disable -->
  <hero-bar>Trend Jumlah Calon Siswa</hero-bar>
  <filter-bar category="calons" @clicked-filter="filters"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-3 sm:w-1/3 md:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure class="w-full">
              <v-chart
                :option="trendcs" autoresize
              />
            </figure>
          </card-component>
      </div>

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <trend-table cat="calons" typedata="trend" :header="header" :sum="sum" :datatable="datatable" :grdtotal="grdtotal"/>
        </card-component>
      </div>

    </div>

  </main-section>
</template>

<script>
/* eslint-disable */
import axios from 'axios'
import { ref, defineComponent, provide, onUpdated, nextTick} from 'vue'
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
import TrendTable from '@/components/calonsiswa/TrendTable'
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
  setup () {
    const store = useStore()
    const titleStack = ref(['Dashboard', 'Calon Siswa'])
    const { dataset, trendcs, datatable, sum, header, grdtotal } = useTrendCs()
    const { filters, filterData } = useFilter()

    console.log(filterData.value)


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
      datatable,
      header,
      sum,
      grdtotal,
      dataset,
      trendcs,
      filterData,
      filters
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
