<template>
<!-- eslint-disable -->
    <hero-bar>Class Occupancy</hero-bar>
    <filter-bar menutp="tingkat" @clicked-filter="filter"/>
    <main-section>
        <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

            <div class="lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-5 sm:w-1/3 md:w-full ">
                <card-component title="Statistik" :icon="mdiChartBar">
                    <v-chart :option="ClassOccOption" />
                </card-component>
            </div>

            <div class="sm:col-span-1 md:col-span-2 xl:col-span-0 sm:max-w-full sm:w-full pr-1">
                <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
                    <!-- <trend-table cat="kelas" typedata="kelas" :header="header" :sum="sum" :datatable="datatable" :grdtotal="grdtotal" :hgtable="changetb"/> -->
                </card-component>
            </div>

        </div>
    </main-section>
</template>

<script>
/* eslint-disable */
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart } from 'echarts/charts';
import {
//   TitleComponent,
    ToolboxComponent,
    DatasetComponent,
    TooltipComponent,
    GridComponent,
    LegendComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';
import axios from 'axios'
import { computed, ref, reactive, defineComponent, watch, watchEffect, onMounted, nextTick } from 'vue'
import { useStore } from 'vuex'
import { mdiTableBorder, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import TrendTable from '@/components/calonsiswa/TrendTable'
import ChartKelasTingkat from '@/components/siswa/ChartKelasTingkat'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import { LabelLayout } from 'echarts/features';
// import useKelas from '@/composables/useKelas'

use([
  ToolboxComponent,
  CanvasRenderer,
  BarChart,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
]);

export default {
    name: 'ClassOccupancy',
    components: {
        TitleSubBar,
        MainSection,
        HeroBar,
        FilterBar,
        CardComponent,
        Notification,
        BottomOtherPagesSection,
        TrendTable,
        ChartKelasTingkat,
        VChart
    },
    setup(props) {
        const store = useStore()
        const titleStack = ref(['Dashboard', 'Siswa'])
        const changetb = ref('on')
        const seriesdata = ref([])
        const datadep = ref([])

        // const { kelasOption, dataset, datatable, sum, header, grdtotal } = useKelas()
        const ClassOccOption = reactive({
            tooltip: {
                trigger: 'item',
            },
            grid: {
                left: '3%',
                right: '2%',
                bottom: '2%',
                containLabel: true
            },
            xAxis: [{
                type: 'category',
                axisLine: { onZero: true },
                splitLine: { show: false },
                splitArea: { show: false },
                data: datadep
            }],
            yAxis: {
                type: 'value'
            },
            series: seriesdata
        })

        // const color = () => {
        //     var arrobj = seriesdata.value
        //     var letters = '0123456789ABCDEF'.split('');
        //     var colors = [];
        //     for (var o = 0; o < arrobj.length; o++){
        //         var color = '#';
        //         for (var i = 0; i < 6; i++ ) {
        //             color += letters[Math.floor(Math.random() * 16)];
        //         }
        //         colors.push(color)
        //     }

        //     return colors
        // }

        const series = computed(() => {
            const datases = seriesdata.value
            var dtFilterTk = datases.filter(({data}) => { return data[0].includes(0) })
            var dtFilterSd = datases.filter(({data}) => { return data[0].includes(1) })

            for (var i = 0; i < datases.length; i++) {
                datases[i].barWidth = '4%';
                datases[i].xAxisIndex = 0;
                datases[i].emphasis = {
                    focus: 'series',
                    blurScope: 'coordinateSystem'
                }
            }

            for(var x = 0; x < dtFilterTk.length; x++) {
                dtFilterTk[x].barWidth = '6%';
                dtFilterTk[x].barCategoryGap = '1%';
            }

            for(var x = 0; x < dtFilterSd.length; x++) {
                dtFilterSd[x].barWidth = '5%';
                dtFilterSd[x].barCategoryGap = '1%';
            }

            return datases
        })

        watchEffect(() => {
            ClassOccOption.series = series.value
            ClassOccOption.xAxis.map(p => p.data).push(datadep.value)
        })

        onMounted(async() => {
            await axios
                .get('/api/v1/siswa/class/')
                .then(res => {
                    seriesdata.value = res.data.arraydata
                    datadep.value = res.data.departemen
                })
                .catch(error => {
                    alert(error.message)
                    console.log(JSON.stringify(error))
                    console.log(JSON.stringify(error.response.data))
                })
        })

        // const filter = async (e) => {
        //     if(e.tahunajaran == 'All' || e.tahunajaran == null || e.tahunajaran == '') {
        //         e.tahunajaran = ''
        //     }

        //     if(e.tingkat == 'All') {
        //         e.tingkat = ''
        //         e.tahunajaran = ''
        //     }

        //     const fil = {
        //         tahunajaran: e.tahunajaran,
        //         tingkat: e.tingkat
        //     }


        //     await axios
        //         .post("/api/v1/siswa/filtertkt/", fil)
        //         .then(res => {
        //             dataset.value = res.data.dataset
        //             datatable.value = res.data.data_table
        //             sum.value = res.data.sum_thj
        //             header.value = res.data.header
        //         })
        //         .catch(error =>{
        //             console.log(JSON.stringify(error))
        //             alert(error.message)
        //         })

        //         await nextTick()
        //             changetb.value = ''
        // }
        //
        return {
            titleStack,
            mdiTableBorder,
            mdiChartBar,
            mdiTableMultiple,
            ClassOccOption,
            // dataset,
            // datatable,
            // header,
            // sum,
            // grdtotal,
            // changetb,
            // filter
        }
    }
};
</script>

<style lang="postcss">
.echarts {
    width: 100%;
    height: 360px;
}

</style>
