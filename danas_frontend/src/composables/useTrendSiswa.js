/* eslint-disable */
import { ref, reactive, watchEffect, computed, onBeforeMount, onMounted} from 'vue'
import _ from 'lodash'
import axios from 'axios'

export default function useTrendSiswa(){
    const dataset = ref([])
    const datatable = ref([])
    const sum = ref([])

    const getData = async () => {
        await axios
            .get('/api/v1/siswa/trend/')
            .then(res => {
                dataset.value = res.data.dataset
                datatable.value = res.data.data_table
                sum.value = res.data.sum_pros
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

    const header = computed(() => {
        const arrobj = datatable.value

        let arr = arrobj.map(p => p.header)

        let finalres = arr.map((v,i) => {
            return v[i]
        })
        return finalres
    })

    const grdtotal = computed(() => {
        const prosTotal = sum.value
        const arrDtable = datatable.value
        const depTotal = arrDtable.map(p => p.grandtotal)

        let sumdep = depTotal.reduce((ac, va) => {
            return ac += va
        },0)

        let sumpros = prosTotal.reduce((a, v) => {
            return a += v
        },0)

        let result = sumpros + sumdep
        return result
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
        header,
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
