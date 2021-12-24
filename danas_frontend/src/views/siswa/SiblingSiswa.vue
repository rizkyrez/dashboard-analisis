<template>
<!-- eslint-disable -->
    <hero-bar>Sibling</hero-bar>
    <filter-bar menutp="sibling" @clicked-filter="filter"/>
    <main-section>

        <div class="grid md:grid-cols-4 sm:grid-cols-none xl:grid-cols-5 gap-2">
            <div class="grid grid-cols-4 lg:w-full sm:col-span-1 md:col-span-2 xl:col-span-3 sm:w-1/3 md:w-full gap-3">
                <div v-for="sm in sum" :key="sm" class="w-full col-span-2 text-center">
                     <card-component v-for="(value, key) in sm" :title="key">
                        <span class="text-5xl font-bold text-center">
                             {{value}}
                        </span>
                    </card-component>
                </div>
            </div>

            <div class="sm:col-span-1 md:col-span-2 xl:col-span-2 sm:max-w-full sm:w-full pr-1">
                <card-component title="Tabel" :icon="mdiTableMultiple" has-table>
                    <sibling-table :header="header" :sum="sum" :datatable="datatable" :grdtotal="grdtotal" />
                </card-component>
            </div>
        </div>

    </main-section>
</template>

<script>
/* eslint-disable */
import { defineComponent, ref, reactive, computed, onMounted, watch, watchEffect, onBeforeMount } from 'vue'
import axios from 'axios'
import { useStore } from 'vuex'
import { mdiTableBorder, mdiChartBar, mdiTableMultiple  } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import SiblingTable from '@/components/siswa/SiblingTable'
// import CardSibling from '@/components/siswa/CardSibling'
import CardComponent from '@/components/CardComponent'
import FilterBar from '@/components/FilterBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import VChart from 'vue-echarts'

export default defineComponent({
    name: 'SiblingSiswa',
    components: {
        TitleSubBar,
        MainSection,
        HeroBar,
        FilterBar,
        CardComponent,
        Notification,
        BottomOtherPagesSection,
        SiblingTable,
        VChart,
    },
    setup() {
        const titleStack = ref(['Dashboard', 'Sibling'])
        const datatable = ref([])
        const sum = ref([])
        const header = ref([])

        const getData = async () => {
            await axios
                .get('/api/v1/siswa/siblings')
                .then(res => {
                    datatable.value = res.data.dataseries
                    sum.value = res.data.sum
                    header.value = res.data.header
                })
                .catch(error => {
                    alert(error.message)
                    console.log(JSON.stringify(error))
                })
        }

        const grdtotal = computed(() => {
            var arrobj = sum.value
            var arr = []
            for (var i = 0; i < arrobj.length; i++) {
                var obj = Object.values(arrobj[i])
                arr.push(obj[0])
            }

            return arr.reduce((acc, v) => {
                return acc += v
            },0)
        })


        watchEffect(() => {
            grdtotal,
            getData()
        })

        onMounted(async() => {
            getData()
        })

        return {
            titleStack,
            mdiTableBorder,
            mdiChartBar,
            mdiTableMultiple,
            header,
            datatable,
            grdtotal,
            sum,
            // kelasOption,
            // dataset,
            // grdtotal,
            // changetb,
            // filter
        }
    },
});
</script>

<style lang="postcss">
body {
    scroll-behavior: smooth;
}
</style>
