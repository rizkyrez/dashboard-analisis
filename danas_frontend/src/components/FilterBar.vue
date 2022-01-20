<template>
  <section class="p-0">
    <card-component>

      <!-- <ul>
        <li
          v-for="(title, index) in titleStack"
          :key="index"
          class="title-stack-item inline-block pr-3 text-2xl text-gray-500 dark:text-gray-400 last:pr-0 last:font-black last:text-black
            ">
          {{ title }}
        </li>
      </ul> -->
      <div class="grid grid-cols-3 grid-flow-col auto-cols-max md:auto-cols-min gap-3">

        <div v-if="category == 'calons'" class="px-3">
        <field label="Departemen">
            <control :options="listdepartemen" v-model="departemen" @change="changeProses()" />
        </field>
        </div>

        <div v-else-if="category == 'pegawai' && menutp != 'agama'" class="px-3">
        <field label="Bagian">
            <control :options="listbagian" v-model="bagian" @change="changeBagian()"/>
        </field>
        </div>

        <div v-if="category == 'pegawai'" class="px-3">
        <field label="Departemen">
            <control :options="listdepartemen3" v-model="departemen" @change="changeTahunajaran2()"/>
        </field>
        </div>

        <div v-else-if="category == 'siswa'" class="px-3">
        <field label="Departemen">
            <control :options="listdepartemenv2" v-model="departemen" @change="changeTahunajaran()" />
        </field>
        </div>

        <div v-if="menutp === 'tingkat' || menutp == 'sibling'" class="px-3">
        <field label="Tingkat">
            <control :options="listTingkat" v-model="tingkat" />
        </field>
        </div>

        <!-- <div v-if="category == 'calons' && menutp != 'achie'" class="px-3"> -->
        <div v-if="category == 'calons' " class="px-3">
        <field label="Proses">
            <control :options="listproses" v-model="proses" :isdisable="valu"/>
        </field>
        </div>

        <!-- <div v-else-if="category == 'calons' && menutp == 'achie'" class="px-3">
        <field label="Proses">
            <control :options="listprosachie" v-model="proses2"/>
        </field>
        </div> -->

        <!-- <div v-if="category == 'siswa' || menutp == 'tingkat'" class="px-3">
        <field label="Tahun Ajaran">
            <control :options="listTahunAjaran" v-model="tahunajaran" />
        </field>
        </div> -->

        <div v-if="category == 'pegawai' && menutp != 'pendidikan'" class="px-3">
        <field label="Tahun Ajaran">
            <control :options="listTahunAjaran2" v-model="tahunajaran" />
        </field>
        </div>

        <div v-if="menutp == 'agama' && category == 'calons'" class="px-3">
        <field label="Agama">
            <control :options="listagama" v-model="agama" />
        </field>
        </div>

        <div v-if="menutp == 'agama' && (category == 'siswa' || category == 'pegawai')" class="px-3">
        <field label="Agama">
            <control :options="listagamav2" v-model="agama" />
        </field>
        </div>

        <!-- <div v-else-if="category == 'calons' && menutp == 'csbachie'" class="px-3">
        <field label="Sibling">
            <control :options="opsiSibling" v-model="sibling"/>
        </field>
        </div> -->

        <div v-else-if="menutp == 'sibling'" class="px-3">
        <field label="aktif">
            <control :options="opsiAktif" v-model="aktifta"/>
        </field>
        </div>

        <div v-if="menutp === 'area'" class="px-3">
        <field label="Area">
            <control :options="opsiArea" v-model="area" />
        </field>
        </div>
<!--
        <div v-if="menutp === 'achievement'" class="px-3">
        <field label="Achievement">
            <control :options="opsiAchiement" v-model="achievement" />
        </field>
        </div> -->
        <div class="flex items-end ">
            <jb-button label="Filter" color="success" @click="clickedFilter()"></jb-button>
        </div>
    </div>

    </card-component>
  </section>
</template>

<script>
/* eslint-disable */
import { computed, ref, watchEffect, onBeforeMount, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Level from '@/components/Level'
import Field from '@/components/Field'
import Control from '@/components/Control'
import JbButton from '@/components/JbButton'
import CardComponent from '@/components/CardComponent'
import useFilter from '@/composables/useFilter'

export default {
  name: 'FilterBar',
  components: {
    Level,
    Field,
    JbButton,
    Control,
    CardComponent
  },
  props: {
    titleStack: {
      type: Array,
      default: () => []
    },
    menutp: String,
    category: String
  },

  emits : ['changeProses','clickedFilter'],
  setup (props,{emit}) {
    const departemen = ref('All')
    const proses = ref('All')
    const proses2 = ref('All')
    const agama = ref('All')
    const tahunajaran = ref('All')
    const tingkat = ref('All')
    const bagian = ref('Semua Bagian')
    const aktifta = ref(1)
    const sibling = ref(1)
    const { getList, listprosachie, listbagian, listdepartemenv2, listdepartemen, listdepartemen3, listproses, listTahunAjaran, listTahunAjaran2, listagama, listagamav2, listTingkat } = useFilter()

    watchEffect(() => {
        departemen.value,
        proses.value,
        proses2.value,
        agama.value,
        tahunajaran.value,
        bagian.value
        if (proses.value == '' || proses.value == null || departemen.value == 'All' ) {
            proses.value = 'All'
        }
    })

    onBeforeMount(getList)

    // const eachpros = computed(() => {
    //     let pros = proses.value
    //     let arr = listproses.value
    //     let match = arr.filter(function(el) {
    //         return el.indexOf(pros) !== -1
    //     })
    //     return match
    // })

    const opsiSibling = [0,1]
    const opsiAktif = [0,1]

    const opsiArea = [
        { id: 1, label: 'Kosambi' },
        { id: 2, label: 'Green Lake City' },
        { id: 3, label: 'TSI' },
        { id: 4, label: 'Poris Indah' },
        { id: 5, label: 'Kosambi Baru' },
        { id: 6, label: 'Green Village' },
    ]

    const changeProses = async () => {
        const fildat = {
            departmen: departemen.value
        }

        await axios
            .post('/api/v1/calons/change/', fildat)
            .then(res => {
                console.log(res);
                listproses.value = res.data.list_proses
            })
            .catch(error => {
                console.log(JSON.stringify(error))
                console.log(JSON.stringify(error.message))
            })

    }

    const changeTahunajaran = async () => {
        const filterdat = {
            departmen: departemen.value
        }

        await axios
            .post('/api/v1/siswa/change/', filterdat)
            .then(res => {
                listTahunAjaran.value = res.data.list_tahunajaran
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error))
            })
    }
    const changeTahunajaran2 = async () => {
        const filterdat = {
            departmen: departemen.value
        }

        await axios
            .post('/api/v1/kepegawaian/change/', filterdat)
            .then(res => {
                listTahunAjaran2.value = res.data.list_tahunajaran
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error))
            })
    }

    const changeBagian = async () => {
        if(e.bagian == 'Semua Bagian') {
            e.bagian = ''  
        }

        const filterdat = {
            bagian: bagian.value
        }

        await axios
            .post('/api/v1/kepegawaian/changebagian/', filterdat)
            .then(res => {
                listdepartemen3.value = res.data.list_dp
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error))
            })
    }

    const valu = computed(() => {
        if(departemen.value == 'All') {
            return true
        }
    })


    const clickedFilter = () => {
        const filterData = {
            departemen: departemen.value,
            proses: proses.value,
            proses2: proses2.value,
            agama: agama.value,
            tahunajaran: tahunajaran.value,
            tingkat: tingkat.value,
            sibling: sibling.value,
            aktifta: aktifta.value,
            bagian: bagian.value
        }
        emit('clickedFilter', filterData)
    };

    return {
      getList,
      listdepartemenv2,
      listdepartemen,
      listdepartemen3,
      listagama,
      listagamav2,
      listproses,
      listTahunAjaran,
      listTahunAjaran2,
      listTingkat,
      listprosachie,
      listbagian,
      opsiSibling,
      opsiAktif,
      changeProses,
      changeTahunajaran,
      changeTahunajaran2,
      changeBagian,
      clickedFilter,
      tahunajaran,
      departemen,
      bagian,
      proses,
      proses2,
      agama,
      tingkat,
      sibling,
      aktifta,
      valu
    }
  }
}
</script>

<style scoped>
/* li.title-stack-item:not(:last-child):after {
  content: '/';
  @apply inline-block pl-3;
} */
</style>
