<template>
    <bar-chart :option="kelasOption" />
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
    setup(props) {
        const { kelasOption } = useKelas()

        return {
            kelasOption
        }
    }
}
</script>
