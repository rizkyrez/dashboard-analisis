<template>
    <bar-chart :option="options" class="hhh"/>
</template>

<script>
/* eslint-disable */
import { ref, reactive, onBeforeMount, onMounted, watchEffect } from 'vue'
import BarChart from '@/components/Charts/BarChart'
import useKelas from '@/composables/useKelas'
export default {
    name: 'ChartKelasTingkat',
    components: {
        BarChart
    },
    props: {
        options: Array || Object
    }
}
</script>

<style lang="postcss">
.hhh {
    height: 550px;
}
</style>
