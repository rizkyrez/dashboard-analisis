<template>
    <!-- eslint-disable -->
  <hero-bar>Failure Report</hero-bar>
    <filter-bar @clicked-filter="filter"/>
  <main-section>

    <div class="grid grid-cols-5 gap-2">

      <div class="col-span-5 max-w-full w-full pr-1">
        <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
          <failure-table/>
        </card-component>
      </div>

    </div>

  </main-section>
</template>

<script>
/* eslint-disable */
import { ref, defineComponent, computed, watchEffect, onBeforeUpdate, onBeforeMount, onMounted, provide, nextTick } from 'vue'
import axios from 'axios'
import {  mdiTableBorder, mdiTableOff, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import FailureTable from '@/components/guru/FailureTable'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'

export default defineComponent({
  name: 'FailureReport',
  components: {
    TitleSubBar,
    MainSection,
    HeroBar,
    FilterBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    FailureTable,
  },
  setup (props) {
    const titleStack = ref(['Dashboard', 'Guru'])
    const flrdata = ref([])

    onMounted(async() => {
        await axios
            .get('/api/v1/guru/failurerp/')
            .then(res => {
                flrdata.value = res.data.dataset
            })
            .catch(error => {
                if (error.message) {
                    console.log("somthieng wrong")
                    console.log(JSON.stringify(error))
                }
            })
    })

    watchEffect(() => {
        flrdata.value
    });

    // const filter = async (e) => {
    //     if(e.tahunajaran == 'All' || e.tahunajaran == null || e.tahunajaran == '') {
    //         e.tahunajaran = ''
    //     }

    //     if(e.departemen == 'All') {
    //         e.departemen = ''
    //         e.tahunajaran = ''
    //     }

    //     if(e.agama == 'All') {
    //         e.agama = ''
    //     }

    //     const fil = {
    //         departmen: e.departemen,
    //         tahunajaran: e.tahunajaran,
    //         agama: e.agama
    //     }

    //     await axios
    //         .post("/api/v1/kepegawaian/filterag/", fil)
    //         .then(res => {
    //             agdata.value = res.data.dataset
    //         })
    //         .catch(error =>{
    //             console.log('something wrong')
    //             console.log(JSON.stringify(error))
    //         })
    // }

    provide('dataTable', flrdata)

    return {
      titleStack,
      mdiTableBorder,
      mdiTableOff,
      mdiChartBar,
      mdiTableMultiple,
      flrdata,
    //   filter,
    }
  },
});
</script>
<style>
/**
 * The default size is 600px×400px, for responsive charts
 * you may need to set percentage values as follows (also
 * don't forget to provide a size for the container).
 */
</style>
