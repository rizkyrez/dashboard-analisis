<template>
<!-- eslint-disable -->
    <hero-bar>Class Occupancy</hero-bar>
    <filter-bar category="siswa" @clicked-filter="filter"/>
    <main-section>
        <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

            <div class="lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-5 sm:w-1/3 md:w-full ">
                <card-component title="Statistik" :icon="mdiChartBar">
                    <v-chart :option="ClassOccOption"/>
                </card-component>
            </div>
            <button-toggle-table color="success"></button-toggle-table>
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
import ButtonToggleTable from '@/components/ButtonToggleTable'
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
        ButtonToggleTable,
        VChart
    },
    setup(props) {
        const store = useStore()
        const titleStack = ref(['Dashboard', 'Siswa'])
        const changetb = ref('on')
        const seriesdata = ref([])
        const datatkt = ref([])

        const ClassOccOption = reactive({
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                    label: {
                        show: true,
                    }
                }
            },
            grid: {
                left: '3%',
                right: '2%',
                bottom: '2%',
                containLabel: true
            },
            xAxis: [{
                type: 'category',
                boundaryGap: true,
                axisLabel: {
                    interval: 'auto',
                    fontSize: 11,
                    fontWeight: 'bold',
                },
                data: []
            }],
            yAxis: {
                type: 'value'
            },
            series: []
        })

        watchEffect(() => {
            ClassOccOption.series = seriesdata.value,
            ClassOccOption.xAxis[0].data = datatkt.value
        })

        onMounted(async() => {
            await axios
                .get('/api/v1/siswa/class/')
                .then(res => {
                    seriesdata.value = res.data.arraydata
                    datatkt.value = res.data.tingkat
                })
                .catch(error => {
                    alert(error.message)
                    console.log(JSON.stringify(error))
                    console.log(JSON.stringify(error.response.data))
                })
        })

        const filter = async (e) => {
            if(e.tahunajaran == 'All' || e.tahunajaran == null || e.tahunajaran == '') {
                e.tahunajaran = ''
            }

            if(e.departemen == 'All') {
                e.departemen = ''
                e.tahunajaran = ''
            }

            const fil = {
                tahunajaran: e.tahunajaran,
                departemen: e.departemen
            }

            await axios
                .post("/api/v1/siswa/filterclassocp/", fil)
                .then(res => {
                    seriesdata.value = res.data.arraydata
                    datatkt.value = res.data.tingkat
                })
                .catch(error =>{
                    console.log(JSON.stringify(error))
                    alert(error.message)
                })

        }

        return {
            titleStack,
            mdiTableBorder,
            mdiChartBar,
            mdiTableMultiple,
            ClassOccOption,
            filter
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
    height: 450px;
}

</style>
