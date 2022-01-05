<template>
<!-- eslint-disable -->
    <hero-bar>Punishment</hero-bar>
    <main-section>

        <div class="grid grid-cols-4 lg:w-full sm:w-full md:w-full h-full gap-3">
            <div v-for="dts in dataset" :key="dts" class="w-full h-full col-span-2 text-center">
                <card-component class="h-auto">
                    <span class="inline-block subpixel-antialiased text-5xl font-bold text-center align-middle my-4">
                            {{dts.value}}
                    </span>
                    <br>
                    <span class="inline-block subpixel-antialiased text-lg font-bold text-center align-bottom">
                         {{dts.dep}}
                    </span>
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
import CardComponent from '@/components/CardComponent'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
import TitleSubBar from '@/components/TitleSubBar'
import VChart from 'vue-echarts'

export default defineComponent({
    name: 'SumPunishment',
    components: {
        TitleSubBar,
        MainSection,
        HeroBar,
        CardComponent,
        Notification,
        BottomOtherPagesSection,
        VChart,
    },
    setup() {
        const titleStack = ref(['Dashboard', 'Punishment'])
        const dataset = ref([])

        const getData = async () => {
            await axios
                .get('/api/v1/kepegawaian/punishment')
                .then(res => {
                    dataset.value = res.data.dataset
                })
                .catch(error => {
                    alert(error.message)
                    console.log(JSON.stringify(error))
                })
        }

        watchEffect(() => {
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
            dataset,
        }
    },
});
</script>

<style lang="postcss">
body {
    scroll-behavior: smooth;
}
</style>
