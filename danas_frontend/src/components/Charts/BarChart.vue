<template>
    <v-chart
    :option="option"
    />
</template>
<script>
/* eslint-disable */
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart } from 'echarts/charts';
import {
//   TitleComponent,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';

use([
  CanvasRenderer,
  BarChart,
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
]);

export default {
    name: 'BarChart',
    components : {
        VChart
    },
    props: {
        option: Object
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
