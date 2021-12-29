/* eslint-disable */
import { ref, reactive, watchEffect, computed, onBeforeMount, onMounted, watch} from 'vue'
import _ from 'lodash'
import axios from 'axios'

export default function useTrendSiswa(){
    const dataset = ref([])
    const datatable = ref([])
    const sum = ref([])
    const header = ref([])

    const getData = async () => {
        await axios
            .get('/api/v1/siswa/trend/')
            .then(res => {
                dataset.value = res.data.dataset
                datatable.value = res.data.data_table
                sum.value = res.data.sum_pros
                header.value = res.data.header
            })
            .catch(error => {
                console.log(JSON.stringify(error))
                console.log(JSON.stringify(error.message))
            })
    }

    const xaxis = computed(() => {
        let arrobj = datatable.value

        let dep = arrobj.map(p => p.departemen)

        return dep
    })

    const legendat = computed(() => {
        let arobj = dataset.value

        let prop = arobj.map(p => p.name)

        return prop
    })

    const tooltip = computed(() => {
        let lenxaxis = xaxis.value.length

        if(lenxaxis == 1) {
            const atr = {
                trigger: 'item',
            }
            return atr
        }
        else {
            const atr = {
               trigger: 'axis',
                axisPointer:{
                    type: 'shadow',
                        label: {
                            show: true,
                        }
                }
            }

            return atr
        }
    })

    // const header = computed(() => {
    //     const arrobj = dataset.value
    //     const arrs = []

    //     let arr = arrobj.map(p => p.name)

    //     return arr
    // })

    const grdtotal = computed(() => {
        const prosTotal = sum.value

        let sumpros = prosTotal.reduce((a, v) => {
            return a += v
        },0)

        return sumpros
    })

    onBeforeMount(getData)

    const trendOption = reactive({
        notMerge:  true,
        tooltip: tooltip,
        grid: {
            left: '6%',
            right: '6%',
            bottom: '20%',
            top:30,
            z: 4
        },
        legend: {
            orient: 'horizontal',
            left: 'center',
            bottom: 'bottom',
            itemGap: 12,
            padding: 8,
            data: legendat,
            // selectedMode: selectmode,
            // selected: selected,
            // top: 'center'
        },
        xAxis: {
            type: 'category',
            boundaryGap: true,
            data: xaxis,
            axisTick: {
                alignWithLabel: true
            }
        },
        yAxis: {type: 'value'},
        series: dataset
    })

    watchEffect(() => {
        dataset.value,
        datatable.value,
        sum.value,
        header.value,
        tooltip,
        legendat,
        xaxis,
        grdtotal,
        trendOption
    })

    return {
        trendOption,
        dataset,
        header,
        datatable,
        sum,
        grdtotal,
        tooltip,
        legendat,
        xaxis,
        getData
    }
  };
