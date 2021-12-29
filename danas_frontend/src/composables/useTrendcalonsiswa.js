/* eslint-disable */
import { ref, reactive, watchEffect, computed, onBeforeMount, onMounted} from 'vue'
import axios from 'axios'

export default function useTrendCs(){
    const dataset = ref([])
    const datatable = ref([])
    const header = ref([])
    const sum = ref([])

    const getData = async () => {
        await axios
            .get('/api/v1/calons/trendcs/')
            .then(res => {
                dataset.value = res.data.dataset
                datatable.value = res.data.data_table
                sum.value = res.data.sum_pros
                header.value = res.data.header
            })
            .catch(error => {
                console.log(JSON.stringify(error))
                console.log(error.message)
            })
    }

    const grdtotal = computed(() => {
        const prosTotal = sum.value

        let sumpros = prosTotal.reduce((a, v) => {
            return a += v
        },0)

        return sumpros
    })


    watchEffect(() => {
        dataset.value
        datatable.value
        sum.value
        header.value
        grdtotal
    })

    onMounted(getData())

    return {
        dataset,
        header,
        datatable,
        sum,
        grdtotal,
    }
};
