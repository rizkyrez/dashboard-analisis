<template>
    <!-- eslint-disable -->
    <hero-bar>Jumlah Kelas Per Tingkat</hero-bar>
    <filter-bar category="siswa" @clicked-filter="filter"/>
    <main-section>

        <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">

            <div class="lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-3 h-full sm:w-1/3 md:w-full ">
                <card-component title="Statistik" :icon="mdiChartBar">
                    <chart-kelas-tingkat :options="kelasOption"/>
                </card-component>
            </div>

            <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
                <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
                    <trend-table cat="kelas" typedata="kelas" :header="header" :sum="sum" :datatable="datatable" :grdtotal="grdtotal" :hgtable="changetb"/>
                </card-component>
            </div>

        </div>

    </main-section>
</template>

<script>
/* eslint-disable */
import axios from 'axios'
import { computed, ref, onBeforeMount, reactive, watchEffect, nextTick } from 'vue'
import { useStore } from 'vuex'
import { mdiTableBorder, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import TrendTable from '@/components/calonsiswa/TrendTable'
import ChartKelasTingkat from '@/components/siswa/ChartKelasTingkat'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import useKelas from '@/composables/useKelas'

export default {
    name: 'StatisClassTingkat',
    components: {
        TitleSubBar,
        MainSection,
        HeroBar,
        FilterBar,
        CardComponent,
        Notification,
        BottomOtherPagesSection,
        TrendTable,
        ChartKelasTingkat
    },
    setup () {
        const store = useStore()
        const titleStack = ref(['Dashboard', 'Siswa'])
        const changetb = ref('on')
        const { kelasOption, dataset, datatable, sum, header, grdtotal } = useKelas()

        const filter = async (e) => {
            if(e.tahunajaran == 'All' || e.tahunajaran == null || e.tahunajaran == '') {
                e.tahunajaran = ''
            }

            if(e.departemen == 'All') {
                e.departemen = ''
                e.tahunajaran = ''
            }


            const fil = {
                tahunajaran: e.tahunajaran,
                departemen: e.departemen
            }


            await axios
                .post("/api/v1/siswa/filtertkt/", fil)
                .then(res => {
                    dataset.value = res.data.dataset
                    datatable.value = res.data.data_table
                    sum.value = res.data.sum_thj
                    header.value = res.data.header
                })
                .catch(error =>{
                    console.log(JSON.stringify(error))
                    alert(error.message)
                })

                await nextTick()
                    changetb.value = ''
        }
        //
        return {
            titleStack,
            mdiTableBorder,
            mdiChartBar,
            mdiTableMultiple,
            kelasOption,
            dataset,
            datatable,
            header,
            sum,
            grdtotal,
            changetb,
            filter
        }
    },

}
</script>
