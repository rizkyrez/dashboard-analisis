import { ref, onBeforeMount, onMounted, watchEffect, provide, toRefs, watch, nextTick, on} from 'vue'
import axios from 'axios'

export default function useAgama(){
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
                    
                })
      }

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
              console.log(dataset.value),
              console.log(dataTable.value),
              console.log(trendcs.value),
              console.log(sum.value),
              console.log(listDep.value),
              console.log(listProp.value)
          },
          {
              flush: 'post'
          }
      );

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
