<template>
    <table>
        <thead>
            <tr>
                <th v-for="header in header" :key="header">{{header}}</th>
            </tr>
        </thead>
        <tbody v-if="nested == 1">
            <tr v-for="row in data" :key="row">
                <td v-for="col in row" :key="col">{{col}}</td>
            </tr>
        </tbody>
        <tbody v-if="nested == 0">
            <tr v-for="row in data" :key="row.name">
                <td>{{ row.name }}</td>
                <td>{{ row.value }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Grand Total</td>
                <td class="font-semibold">{{ grandTotal }}</td>
            </tr>
        </tbody>
    </table>
</template>
<script>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'

export default {
    props: {
        header: Array,
        data: Object,
        nested: Boolean,
        style: Array
    },
    setup(props) {
        const data = ref(props.data)

        const grandTotal = computed(() => {
            var obj = data.value
            return obj.reduce((acc, curV) => {
                return acc += curV.value
            },0)
        })

        return {
            grandTotal
        }
    }
}
</script>
