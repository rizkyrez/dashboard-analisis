<template>
<!-- eslint-disable  -->
    <div class="overflow-auto">
        <table class="border border-collapse overflow-auto w-max-full border-green-400 text-xs">
            <thead>
                <tr class="bg-green-300">

                   <th class="font-semibold">Departemen</th>
                   <th v-for="hdr in header" :key="hdr" class="font-semibold">{{hdr}}</th>
                   <th class="font-semibold">Grand Total</th>
                    <!-- <th class="font-semibold">gelombang 1</th>
                    <th class="font-semibold">GD 1 </th>
                    <th class="font-semibold">Gelombang 1 </th>
                    <th class="font-semibold">Gelombang 1 TA 2019/2020</th>
                    <th class="font-semibold">Gelombang TA 2020/2021</th>
                    <th class="font-semibold">Gelombang 1 TA 2020/2021</th>
                    <th class="font-semibold">Gelombang TA 2021/2022</th>
                    <th class="font-semibold">Grand Total</th> -->
                </tr>
            </thead>
            <tbody>
                <!-- eslint-disable -->
                <tr v-for="(rowData,y) in dataTable" :key="rowData.departemen">
                    <td>{{rowData.departemen}}</td>
                    <td v-for="detailTotal in rowData.total" :key="detailTotal">{{detailTotal}}</td>
                    <td >{{rowData.grandtotal}}</td>

                    <!-- <td v-for="col in">{{ datap.data[i] }}</td> -->
                    <!-- <td>
                        TK
                    </td>
                    <td>75</td>
                    <td>0</td>
                    <td>0</td>
                    <td>85</td>
                    <td>57</td>
                    <td>0</td>
                    <td>15</td>
                    <td>232</td> -->
                </tr>
                <!-- <tr >
                    <td>2</td>
                    <td data-label="departemen">
                        SD
                    </td>
                    <td data-label="g21">0</td>
                    <td data-label="g22">34</td>
                    <td data-label="g21">0</td>
                    <td data-label="g21">34</td>
                    <td data-label="g22">0</td>
                    <td data-label="g21">108</td>
                    <td data-label="g22">93</td>
                    <td data-label="grdtotal1">269</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td data-label="departemen">
                        SMP
                    </td>
                    <td data-label="g21">0</td>
                    <td data-label="g22">0</td>
                    <td data-label="g21">10</td>
                    <td data-label="g21">16</td>
                    <td data-label="g22">0</td>
                    <td data-label="g21">84</td>
                    <td data-label="g22">47</td>
                    <td data-label="grdtotal1">157</td>
                </tr>

                <tr >
                    <td>4</td>
                    <td data-label="departemen">
                        SMA
                    </td>
                    <td data-label="g21">7</td>
                    <td data-label="g22">0</td>
                    <td data-label="g21">0</td>
                    <td data-label="g21">0</td>
                    <td data-label="g22">0</td>
                    <td data-label="g21">37</td>
                    <td data-label="g22">31</td>
                    <td data-label="grdtotal1">75</td>
                </tr> -->
                <tr >
                    <td class="font-semibold" >
                        Grand Total
                    </td>
                    <td v-for="stpros in sumTotalPros" :key="stpros">{{stpros}}</td>
                    <!-- <td data-label="g21">82</td>
                    <td data-label="g22">34</td>
                    <td data-label="g21">10</td>
                    <td data-label="g21">135</td>
                    <td data-label="g22">57</td>
                    <td data-label="g21">229</td>
                    <td data-label="g22">186</td> -->
                    <td >
                        {{grdtotal}}
                    </td>
                </tr>
            <!-- <tr v-for="client in itemsPaginated" :key="client.id">
            <checkbox-cell v-if="checkable" @checked="checked($event, client)"/>
            <td class="image-cell">
                <user-avatar :username="client.name" class="image" />
            </td>
            <td data-label="Name">{{ client.name }}</td>
            <td data-label="Company">{{ client.company }}</td>
            <td data-label="City">{{ client.city }}</td>
            <td data-label="Progress" class="progress-cell">
                <progress max="100" :value="client.progress">{{ client.progress }}</progress>
            </td>
            <td data-label="Created">
                <small class="text-gray-500 dark:text-gray-400" :title="client.created">{{ client.created }}</small>
            </td>
            <td class="actions-cell">
                <jb-buttons type="justify-start lg:justify-end" no-wrap>
                <jb-button class="mr-3" color="success" :icon="mdiEye" small @click="isModalActive = true" />
                <jb-button color="danger" :icon="mdiTrashCan" small @click="isModalDangerActive = true" />
                </jb-buttons>
            </td>
            </tr> -->
            </tbody>
        </table>
    </div>
</template>

<script>
/* eslint-disable */
import { computed, ref, watchEffect, toRefs, inject, watch } from 'vue'
import { useStore } from 'vuex'
import { mdiEye, mdiTrashCan } from '@mdi/js'
import ModalBox from '@/components/ModalBox'
import CheckboxCell from '@/components/CheckboxCell'
import Level from '@/components/Level'
import JbButtons from '@/components/JbButtons'
import JbButton from '@/components/JbButton'
import UserAvatar from '@/components/UserAvatar'

export default {
  name: 'TrendTable',
  components: {
    ModalBox,
    CheckboxCell,
    Level,
    JbButtons,
    JbButton,
    UserAvatar
  },
  props: {
    // arrdata: Array,
    checkable: Boolean,
  },
  setup (props) {
    const store = useStore()
    const darkMode = computed(() => store.state.darkMode)
    const items = computed(() => store.state.clients)
    const isModalActive = ref(false)
    const isModalDangerActive = ref(false)
    const checkedRows = ref([])
    const arrdataset = inject('arrdata')
    const dataTable = inject('dataTable')
    const sumTotalPros = inject('sum_proses')
    const noinc = 1
    const header = inject('header')

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

     watchEffect(() => {
        grdtotal
    })

    // const listTotal = (value) => {
    //     const sum = computed(() => {
    //         value.reduce((acc,curVal) => {
    //             console.log(typeof(curVal))
    //         },0)
    //     })

    //     return sum
    // }
    // const sumByProses = computed(() => {
    //   return this.cart.items.reduce((acc, curVal) => {
    //     return acc += curVal.product.price * curVal.quantity
    //   }, 0)
    // })

    const itemsPaginated = computed(
      () => items.value.slice(perPage.value * currentPage.value, perPage.value * (currentPage.value + 1))
    )

    const numPages = computed(() => Math.ceil(items.value.length / perPage.value))

    const pagesList = computed(() => {
      const pagesList = []

      for (let i = 0; i < numPages.value; i++) {
        pagesList.push(i)
      }

      return pagesList
    })

    const remove = (arr, cb) => {
      const newArr = []

      arr.forEach(item => {
        if (!cb(item)) {
          newArr.push(item)
        }
      })

      return newArr
    }

    const checked = (isChecked, client) => {
      if (isChecked) {
        checkedRows.value.push(client)
      } else {
        checkedRows.value = remove(checkedRows.value, row => row.id === client.id)
      }
    }

    return {
      darkMode,
      isModalActive,
      isModalDangerActive,
      noinc,
      numPages,
      checkedRows,
      itemsPaginated,
      pagesList,
      checked,
      mdiEye,
      mdiTrashCan,
      arrdataset,
      header,
      dataTable,
      sumTotalPros,
      grdtotal
    //   listTotal
    }
  },

}
</script>
