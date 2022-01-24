<template>
    <v-chart
    :class="cheight"
    :option="option"
    />
</template>
<script>
/* eslint-disable */
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart,LineChart } from 'echarts/charts';
import { UniversalTransition } from 'echarts/features';
import {
//   TitleComponent,
  DatasetComponent,
  TooltipComponent,
  ToolboxComponent,
  GridComponent,
  LegendComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';

use([
  CanvasRenderer,
  BarChart,
  LineChart,
  DatasetComponent,
  TooltipComponent,
  ToolboxComponent,
  GridComponent,
  LegendComponent,
  UniversalTransition
]);

export default {
    name: 'BarChart',
    components : {
        VChart
    },
    props: {
        option: Object,
        cheight: String
    }
}
</script>
<style lang="postcss">
    .echarts {
        width: 100%;
        height: 40vw;
        min-height: 400px;
        max-height: 1500px;
    }
</style>
