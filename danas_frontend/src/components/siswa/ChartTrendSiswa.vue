<template >
    <bar-chart :option="trendOption" />
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
    setup(props) {
        const { trendOption } = useTrendSiswa()

        return {
            trendOption
        }
    }
}
</script>
