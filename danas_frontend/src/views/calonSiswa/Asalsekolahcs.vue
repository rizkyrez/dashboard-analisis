<template>
    <!-- eslint-disable -->
  <hero-bar>Agama</hero-bar>
  <filter-bar category="calons" @change-proses="changepros" @clicked-filter="filter"/>
  <main-section>

    <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

      <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
          <card-component title="Statistik" :icon="mdiChartBar">
            <figure>
              <v-chart ref="piechart"
                :option="asalSekolah"
                @click="expands"
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
</template>

<script>
/* eslint-disable */
import { useStore } from 'vuex'
import { init as initChart, use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { LabelLayout } from 'echarts/features';
import { PieChart, BarChart } from 'echarts/charts';
import {
  TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
  GraphicComponent,
  DataZoomComponent
} from 'echarts/components';
import VChart from 'vue-echarts';
import { ref, defineComponent, computed, watchEffect, onBeforeUpdate, onBeforeMount, onMounted, provide, nextTick, reactive, h} from 'vue'
import axios from 'axios'
import { mdiTableBorder, mdiChartBar, mdiTableMultiple } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import AsalSekolahTable from '@/components/calonsiswa/AsalSkTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import PopupChart from '@/components/Charts/PopupChart'

use([
  CanvasRenderer,
  BarChart,
  PieChart,
  LabelLayout,
  TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
   GraphicComponent,
   DataZoomComponent
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
        PopupChart,
        VChart
    },
    setup (props) {
        const titleStack = ref(['Dashboard', 'Calon Siswa'])

        const store =  useStore()
        const asalSekolah = ref()
        const askdata = ref([])
        const seriesdata = ref([])
        const listDep = ref([])
        const listProp = ref([])
        const listAsk = ref([])
        const piechart = ref()

        // const expands = (e) => {
        //     const data = e.series

        //     const arr = data.map(p => p.data)

        //     const ext = arr.filter(({name}) => name.toLowerCase().includes(('External').toLowerCase()))

        //     console.log(expands)
        // }
        // const dom = document.getElementById('char')
        // const chart = initChart(dom)

        const sumva = computed(() => {
            let arrobj = askdata.value

            let result = arrobj.filter(({name}) => !name.toLowerCase().includes(('Narada').toLowerCase()))
                                .reduce((acc, v) =>{
                                    return acc += v.value
                                },0)

            let result2 = arrobj.filter(({name}) => name.toLowerCase().includes(('Narada').toLowerCase()))
                                .reduce((a, v) => {
                                    return a += v.value
                                },0)

            const dataset = [];

            dataset.push(
                {
                    name: 'Internal',
                    value: result2
                },
                {
                    name: 'External',
                    value: result
                }
            )

            return dataset
        })


        const option1 = ref({
            title: {
                text: "Total Asal Sekolah",
                left: "center",
            },
            tooltip: {
                trigger: "item",
                formatter: "{b} : {c} ({d}%)"
            },
            legend: {
                type: 'scroll',
                orient: 'horizontal',
                right: 10,
                bottom: -5,
                itemHeight: 12,
                height: 100
            },
            animationDurationUpdate: 500,
            dataZoom:[
                {
                    show: false,
                },
                {
                    type: 'inside',
                },
                {
                    show: false,
                }
            ],
            xAxis: { show:false },
            yAxis: { show:false},
            series: {
                    type: "pie",
                    radius: "55%",
                    id:'external',
                    label: {
                        position: 'outer',
                        alignTo: 'labelLine',
                        bleedMargin: 6
                    },
                    data: sumva,
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
            universalTransition: {
                enabled: true,
                divideShape: 'clone'
            }

        })

        onMounted(async() => {
            await axios
                .get('/api/v1/calons/asalsekolah/')
                .then(res => {
                    askdata.value = res.data.askdata
                    seriesdata.value = res.data.dataseries
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

            asalSekolah.value = option1.value
        })

        watchEffect(() => {
            listDep.value,
            listProp.value,
            listAsk.value,
            console.log(askdata.value),
            seriesdata.value,
            asalSekolah.value,
            piechart
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

            await nextTick()
                listProp.value = listProp.value
        }

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

        const expands = (e) => {
            if(e.name == 'External') {
                const arrobj = askdata.value

                let result = arrobj.filter(({name}) => !name.toLowerCase().includes(('Narada').toLowerCase()))

                asalSekolah.value = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer:{
                            type: 'shadow',
                            label: {
                                show: true,
                            },
                        },
                        formatter: "{b} : {c}"
                    },
                    xAxis: {
                        show:true,
                        type: 'category',
                        boundaryGap: true,
                        data: result.map(p => p.name),
                    },
                    yAxis: {
                        show:true,
                        type: 'value'
                    },
                    dataZoom: [
                        {
                            show: true,
                            start: 0,
                            end: 100,
                            height: 20
                        },
                        {
                            type: 'inside',
                            start: 100,
                            end: 100
                        },
                        {
                            show: true,
                            yAxisIndex: 0,
                            filterMode: 'empty',
                            width: 15,
                            height: '70%',
                            showDataShadow: false,
                            left: '93%'
                        }
                    ],
                    series: {
                        type: 'bar',
                        id: 'external',
                        data: result.map(p => p.value),
                        universalTransition: {
                        enabled: true,
                        divideShape: 'clone'
                        }
                    },
                    graphic: [
                        {
                        type: 'text',
                        left: 50,
                        top: 20,
                        style: {
                            text: 'Back',
                            fontSize: 18
                        },
                            onclick: function () {
                                asalSekolah.value = option1.value;
                            }
                        }
                    ]
                }
            }
        }

        provide('dataTable', sumva)
        provide('list_departemen', listDep)
        provide('list_proses', listProp)
        provide('list_asalsekolah',listAsk)

        return {
            titleStack,
            mdiTableBorder,
            mdiChartBar,
            mdiTableMultiple,
            askdata,
            seriesdata,
            asalSekolah,
            filter,
            changepros,
            expands,
            piechart
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
  min-height: 550px;
  max-height: 100%;
}
</style>
