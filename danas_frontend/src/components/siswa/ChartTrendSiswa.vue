<template >
    <bar-chart :option="options" :opsic="chartoption" />
</template>
<script>
/* eslint-disable */
import { ref, reactive, onBeforeMount, onMounted, watchEffect } from 'vue'
import BarChart from '@/components/Charts/BarChart'
import useTrendSiswa from '@/composables/useTrendSiswa'

export default {
    name: 'ChartTrendSiswa',
    components: {
        BarChart
    },
    props: {
        options: Array,
        chartoption: String,
    },
}
</script>
