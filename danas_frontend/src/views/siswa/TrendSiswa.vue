<template>
  <!-- eslint-disable -->
  <hero-bar>Trend Jumlah Siswa</hero-bar>
  <filter-bar category="siswa" @clicked-filter="filter"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

        <div class="lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-3 sm:w-1/3 md:w-full ">
            <card-component title="Statistik" :icon="mdiChartBar">
                <chart-trend-siswa :options="trendOption"/>
            </card-component>
        </div>

        <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
            <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
                <trend-table cat="siswa" typedata="trend" :header="header" :sum="sum" :datatable="datatable" :grdtotal="grdtotal"/>
            </card-component>
        </div>

    </div>

  </main-section>
</template>

<script>
/* eslint-disable */
import axios from 'axios'
import { computed, ref, onBeforeMount, reactive, watchEffect, nextTick, watch } from 'vue'
import { useStore } from 'vuex'
import { mdiTableBorder, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import TrendTable from '@/components/calonsiswa/TrendTable'
import ChartTrendSiswa from '@/components/siswa/ChartTrendSiswa'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import useTrendSiswa from '@/composables/useTrendSiswa'

// import useFilter from '@/composables/useFilter'

export default {
  name: 'TrendSiswa',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    TrendTable,
    ChartTrendSiswa
  },
  setup () {
    const store = useStore()
    const titleStack = ref(['Dashboard', 'Siswa'])
    const { trendOption, dataset, datatable, header, sum, grdtotal } = useTrendSiswa()

    watchEffect(() => {
      dataset.value,
      datatable.value,
      header.value,
      sum.value,
      grdtotal.value,
      trendOption
    })

    const filter = async (e) => {
        if(e.tahunajaran.includes("(Aktif)") == true) {
            e.tahunajaran = e.tahunajaran.split(" (Aktif)").join("") 
        }
        
        if(e.tahunajaran == 'All' || e.tahunajaran == null || e.tahunajaran == '') {
            e.tahunajaran = ''
        }

        if(e.departemen == 'All') {
            e.departemen = ''
        }

        const fil = {
            departmen: e.departemen,
            tahunajaran: e.tahunajaran
        }

        await axios
            .post("/api/v1/siswa/filtertrend/", fil)
            .then(res => {
                dataset.value = res.data.dataset
                datatable.value = res.data.data_table
                sum.value = res.data.sum_ta
                header.value = res.data.header
            })
            .catch(error =>{
                console.log(JSON.stringify(error))
                alert(error.message)
            })
            
            const xaxis = [];
            const legendat = [];

            let arrobj = datatable.value;
            let dep = arrobj.map(p => p.departemen);
            xaxis.push(dep);

            let arobj = dataset.value;
            let prop = arobj.map(p => p.name);
            legendat.push(prop);

            let lenxaxis = xaxis.length
            var toltp = () => {
                if(lenxaxis == 1) {
                    const atr = {
                        trigger: 'item',
                    }
                    return atr
                }
                else {
                    const atr = {
                      trigger: 'axis',
                        axisPointer:{
                            type: 'shadow',
                                label: {
                                    show: true,
                                }
                        }
                    }

                    return atr
                };
            }
            
            const tooltip = toltp();

            if(e.tahunajaran == '') {
              trendOption.value = {
                  tooltip: {
                    trigger: 'axis'
                  },
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
                  },
                  xAxis: {
                      type: 'category',
                      boundaryGap: false,
                      data: header,
                      axisTick: {}
                  },
                  yAxis: {type: 'value'},
                  series: dataset
              }
            }
            else if(e.tahunajaran != '') {
              trendOption.value = {
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
              }
            }
        
    }


    return {
      titleStack,
      mdiTableBorder,
      mdiChartBar,
      mdiTableMultiple,
      trendOption,
      datatable,
      header,
      sum,
      grdtotal,
      filter
    }
  },

};
</script>

<style lang="postcss">
html {
    scroll-behavior: smooth;
}
</style>
