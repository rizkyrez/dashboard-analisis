import { ref, onBeforeMount, onMounted, watchEffect, provide, toRefs, watch, nextTick, on} from 'vue'
import axios from 'axios'

export default function useTrendCs(){
    const dataset = ref([])
    const dataTable = ref([])
    const trendcs = ref()
    const sum = ref([])

    const getDataset = async () => {
        await axios
            .get('/api/v1/calons/trendcs')
            .then(res => {
                dataset.value = res.data.dataset
                dataTable.value = res.data.data_table
                sum.value = res.data.sum_pros
            })
            .catch(error => {
                console.log(JSON.stringify(error))
            })
    }

    const xaxis = computed(() => {
        let arrobj = dataTable.value

        let dep = arrobj.map(p => p.departemen)

        return dep
    })

    const legendat = computed(() => {
        let arobj = dataset.value

        let prop = arobj.map(p => p.name)

        return prop
    })

    const grandtotal = computed(() => {

    })

    const tooltip = computed(() => {
        let lenxaxis = xaxis.value.length

        if(lenxaxis == 1) {
            const atr = {
                trigger: 'item'
            }
            return atr
        }
        else {
            const atr = {
               trigger: 'axis',
                axisPointer:{
                    type: 'shadow',
                        label: {
                            show: true
                        }
                }
            }

            return atr
        }
    })

    const grdtotal = computed(() => {
        const prosTotal = sumTotalPros.value
        const arrDtable = dataTable.value
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

      onBeforeMount(async () => {
          await axios
          .get('/api/v1/calons/trendcs')

          .catch(error =>{
              if (error.message) {
                  this.errors.push('something wrong')
                  console.log(JSON.stringify(error))
              }
          })

          trendcs.value = {
              aria: {
                  show: true
              },
              tooltip: {
                  trigger: 'axis',
                  axisPointer:{
                  type: 'shadow',
                      label: {
                          show: true
                      }
                  }
              },
              grid: {
                  left: '6%',
                  right: '6%',
                  bottom: '6%',
                  top:80,
                  z: 4
              },
              legend: {
                  orient: 'horizontal',
                  left: 'center',
                  top: 'top',
                  itemGap: 12,
                  padding: 8
                  // top: 'center'
              },
              xAxis: {type: 'category'},
              yAxis: {type: 'value'},
              series: dataset
          };
      })

      watchEffect(() => {
              dataset.value,
              dataTable.value,
              trendcs.value,
              sum.value,
              listDep.value,
              listProp.value
          });


      // const dataseries =  computed({
      //         get: () => dataset.value,
      //         set: val => {
      //             dataset.value = val
      //         }
      //     });

      provide('arrdata', dataset)
      provide('dataTable', dataTable)
      provide('sum_proses', sum)
      provide('list_departemen', listDep)
      provide('list_proses', listProp)

      return {
        trendcs,
        dataset,
        dataTable,
        sum
      }
  };
