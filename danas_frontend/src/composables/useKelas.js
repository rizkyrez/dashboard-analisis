/* eslint-disable */
import { ref, reactive, watchEffect, computed, onBeforeMount, onMounted} from 'vue'
import _ from 'lodash'
import axios from 'axios'

export default function useKelas(){
    const dataset = ref([])
    const datatable = ref([])
    const sum = ref([])

    const getData = async () => {
        await axios
            .get('/api/v1/siswa/jmlkelastingkat/')
            .then(res => {
                dataset.value = res.data.dataset
                datatable.value = res.data.data_table
                sum.value = res.data.sum_thj
            })
            .catch(error => {
                console.log(JSON.stringify(error))
                console.log(JSON.stringify(error.message))
            })
    }

    const yaxis = computed(() => {
        let arrobj = datatable.value

        let dep = arrobj.map(p => p.tingkat)

        return dep
    })

    const legendat = computed(() => {
        let arobj = dataset.value

        let prop = arobj.map(p => p.name)

        return prop
    })

    const tooltip = computed(() => {
        let lenxaxis = yaxis.value.length

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
        const arrobj = dataset.value

        let arr = arrobj.map(p => p.name)

        return arr
    })

    const grdtotal = computed(() => {
        const thjTotal = sum.value

        let sumthj = thjTotal.reduce((a, v) => {
            return a += v
        },0)

        return sumthj
    })

    onBeforeMount(getData)

    const kelasOption = reactive({
        notMerge:  true,
        tooltip: tooltip,
        grid: {
            left: '6%',
            right: '6%',
            bottom: '20%',
            top:30,
            z: 4,
            containLabel:true
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
        xAxis: { type: 'value' },
        yAxis: {
            type: 'category',
            data: yaxis,
            axisLabel: {
                interval: 0
            }
        },
        series: dataset
    })

    const tes = computed(() => {
        const datat =  dataset.value

        let tess = datat.map(p => p.name)

        return tess
    })

    console.log(tes)

    watchEffect(() => {
        dataset.value,
        datatable.value,
        sum.value,
        header,
        tooltip,
        legendat,
        yaxis,
        grdtotal,
        kelasOption
    })

    return {
        kelasOption,
        dataset,
        header,
        datatable,
        sum,
        grdtotal,
        tooltip,
        legendat,
        yaxis,
        getData
    }
  };
