/* eslint-disable */
import {ref, toRefs, reactive, watchEffect, onBeforeMount, onMounted, nextTick, watch, computed} from 'vue'
import axios from 'axios'

export default function useFilter() {
    const listdepartemen = ref([])
    const listproses = ref([])
    const listagama = ref([])
    const listdepartemenv2 = ref([])
    const listTahunAjaran = ref([])
    const listagamav2 = ref([])
    const listTingkat = ref([])
    const listprosachie = ref([])
    const listbagian = ref([])
    const listTahunAjaran2 = ref([])
    const listdepartemen3 = ref([])

    const filterData = ref([])

    const getList = async () => {
        const getListFilterCalons = async () => {
            return await axios.get('/api/v1/calons/listfilter/');
        }
        const getListFilterSiswa = async () => {
            return await axios.get('/api/v1/siswa/listfiltersiswa');
        }
        const getListProsAchie = async () => {
            return await axios.get('/api/v1/calons/achievement')
        }

        const getListPegawai = async () => {
            return await axios.get('/api/v1/kepegawaian/listfilterpegawai')
        }

        Promise.all([getListFilterCalons(), getListFilterSiswa(), getListProsAchie(),getListPegawai()])
            .then(function(results) {
                const listcalons = results[0];
                const listsiswa = results[1];
                const listachie = results[2];
                const listpegawai = results[3]

                listdepartemen.value = listcalons.data.list_departemen
                listproses.value = listcalons.data.list_proses
                listprosachie.value = listachie.data.header
                listagama.value = listcalons.data.list_agama
                listdepartemenv2.value = listsiswa.data.list_dp
                listTahunAjaran.value = listsiswa.data.list_tahunajaran
                listagamav2.value = listsiswa.data.list_ag
                listTingkat.value = listsiswa.data.list_tingkat
                listbagian.value = listpegawai.data.list_bg
                listTahunAjaran2.value = listpegawai.data.list_tahunajaran
                listdepartemen3.value = listpegawai.data.list_dp

            })
            .catch(error => {
                alert(error.message)
                console.log(error.message)
                console.log(JSON.stringify(error))
            });
    }

    const filters = async (e) => {
        if(e.proses == 'All' || e.proses == null || e.proses == '') {
            e.proses = ''
        }

        if(e.departemen == 'All') {
            e.departemen = ''
            e.proses = ''
        }

        const fil = {
            departmen: e.departemen,
            proses: e.proses
        }

        await axios
            .post("/api/v1/calons/filter/", fil)
            .then(res => {
                filterData.value = res.data.dataset
            })
            .catch(error =>{
                console.log('something wrong')
                console.log(JSON.stringify(error.message))
            })
    }

    watch(filterData, (newv, prevv) => {
        filterData.value = newv
    },{
        immediate:true
    })

    watchEffect(() => {
        listdepartemen.value,
        listagama.value,
        listproses.value,
        listdepartemenv2.value,
        listTahunAjaran.value,
        listagamav2.value,
        listTingkat.value,
        listprosachie.value,
        listbagian.value,
        listTahunAjaran2.value,
        listdepartemen3.value
    })

    return {
        listagama,
        listdepartemenv2,
        listproses,
        listTahunAjaran,
        listTahunAjaran2,
        listdepartemen,
        listdepartemen3,
        listbagian,
        listagamav2,
        listTingkat,
        listprosachie,
        filterData,
        filters,
        getList,
        // dataset,
        // header,
        // datatable,
        // sum,
        // getListFilterCalons,
        // getListFilterSiswa
        // filters
    }
}
