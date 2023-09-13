<template>
    <gaudge-chart :options="options"/>
</template>

<script>
import { ref, reacitve, computed, watch } from 'vue'
import BarChart from '@/components/Charts/BarChart'

export default {
    components: {
        BarChart
    },
    props: {
        options: Object,
    },
    setup(props) {
    }
}
</script>

