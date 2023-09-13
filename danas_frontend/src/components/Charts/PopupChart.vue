<template>
    <!-- eslint-disable -->
    <overlay v-if="state == true" @overlay-click="cancel">
            <card-component
                v-if="state == true"
                class="shadow-lg w-full max-h-modal -ml-12 md:w-3/5 lg:w-1/3 z-50"
                :header-icon="mdiClose"
                @header-icon-click="cancel"
            >
                <div class="space-y-3">
                    <v-chart :option="chartexbar"/>
                </div>

            </card-component>
    </overlay>
</template>

<script>
/* eslint-disable */import { init as initChart, use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { LabelLayout } from 'echarts/features';
import { BarChart } from 'echarts/charts';
import {
  TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
} from 'echarts/components';
import VChart from 'vue-echarts';
import { ref, defineComponent, computed, watchEffect, onBeforeUpdate, onBeforeMount, onMounted, inject, nextTick, reactive, h} from 'vue'
import { mdiClose } from '@mdi/js'
import CardComponent from '@/components/CardComponent'
import Overlay from '@/components/Overlay'

use([
  CanvasRenderer,
  BarChart,
  LabelLayout,
  TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
]);

export default defineComponent({
    name: 'PopupChart',
    components: {
        CardComponent,
        VChart,
        Overlay
    },
    props: {
        hasCancel: Boolean,
        state: Boolean,
        modelValue: [String, Number, Boolean]
    },
    emits: ['update:modelValue', 'cancel', 'confirm'],
    setup(props, {emit}) {

        const confirmCancel = () => {
            props.state = false
        }

        const seriesdata = inject('datase')

        const sumva = computed(() => {
            let arrobj = seriesdata.value

            let result = arrobj.filter(({name}) => !name.toLowerCase().includes(('Narada').toLowerCase()))
                                .reduce((acc, v) =>{
                                    return v.value
                                },[])

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

        const chartexbar = reactive({
            tooltip: {
                trigger: 'item'
            },
            grid: {
                left: '6%',
                right: '6%',
                bottom: '20%',
                top:30,
                z: 4
            },
            xAxis: {
                type: 'category',
                data: ['te','tet','tes','t']
            },
            yAxis: {},
            series: [
                {
                    name: 'tes',
                    type: 'bar',
                    data: [90,45,345,45]
                },
            ]
        })

        const cancel = () => confirmCancel()

        watchEffect(() => {
            console.log(seriesdata.value)
            chartexbar
        })

        return {
            chartexbar,
            mdiClose,
            cancel
        }
    },
});
</script>

<style lang="postcss">
.echarts {
  width: auto;
  height: 150px;
}
</style>
