<template>
    <!-- eslint-disable -->
    <hero-bar>CSB Potensial vs Achievement Internal</hero-bar>
    <filter-bar category="calons" menutp="csbachie" @clicked-filter="filter"/>
    <main-section>

        <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

            <div class="sm:col-span-1 md:col-span-2 xl:col-span-3 sm:max-w-full sm:w-full ">
                <card-component title="Statistik" :icon="mdiChartBar">
                    <figure>
                    <v-chart
                        :option="csbachievement"
                    />
                    </figure>
                </card-component>
            </div>

            <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
                <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
                <!-- <achievement-table :header="header" :datatable="datatable" :grdtotal="grdtotal" :percentage="percentase"/> -->
                <achievement-table :header="header" :datatable="results" :grandtotal="datatable" :percentage="percentase"/>
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
import { mdiTableBorder, mdiTableOff, mdiChartBar, mdiTableMultiple } from '@mdi/js'
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
    name: 'CsbAchievement',
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
        const datatable = ref([0,0])
        const results = ref()
        const header = ref(['Target CSB Potensial','Achievement CSB Internal'])

        const percentase = computed(() => {
            const arrobj = datatable.value

            let result = ((arrobj[1] / arrobj[0]) * 100 || 0).toFixed(2)

            return result
        })

        const grdtotal = computed(() => {
            const arrobj = datatable.value

            let result = arrobj.reduce((a,v) => {
                return a += v
            },0)

            return result
        })

        const csbachievement = reactive({
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
                    max: 100,
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
                        value: percentase,
                        name: 'Achievement'
                        }
                    ],
                }
            ]
        })

        watchEffect(() => {
            datatable.value = datatable.value
            header,
            percentase,
            csbachievement
        })

        onMounted(async () => {
            await axios
                .post('/api/v1/calons/csbpotensial')
                .then(res => {
                    datatable.value = res.data.data_table
                    results.value = res.data.datatable
                })
                .catch(error => {
                    alert(error.message)
                    console.log(JSON.stringify(error))
                })
        })


        const filter = async (e) => {
            // if(e.proses2 == 'All' || e.proses2 == null || e.proses2 == '') {
            //     e.proses = ''
            // }

            if(e.departemen == 'All') {
                e.departemen = null
                e.proses = null
            }

            const fil = {
                departemen: e.departemen,
                proses_id: e.proses?e.proses.id:null
            }

            console.log(fil);

            
            await axios
                .post("/api/v1/calons/csbpotensial/", fil)
                .then(res => {
                    datatable.value = res.data.data_table
                    results.value = res.data.datatable
                })
                .catch(error =>{
                    console.log('something wrong')
                    console.log(JSON.stringify(error))
                })
        }

        return {
            titleStack,
            mdiTableBorder,
            mdiTableOff,
            mdiChartBar,
            mdiTableMultiple,
            datatable,
            percentase,
            datatable,
            grdtotal,
            header,
            csbachievement,
            filter,
            results
        }
    },
});

</script>
<style lang="postcss">

</style>
