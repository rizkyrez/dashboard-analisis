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

    const filterData = ref([])
    // const dataset = ref([])
    // const datatable = ref([])
    // const header = ref([])
    // const sum = ref([])

    const getList = async () => {
        const getListFilterCalons = async () => {
            return await axios.get('/api/v1/calons/listfilter/');
        }
        const getListFilterSiswa = async () => {
            return await axios.get('/api/v1/siswa/listfiltersiswa');
        }

        Promise.all([getListFilterCalons(), getListFilterSiswa()])
            .then(function(results) {
                const listcalons = results[0];
                const listsiswa  = results[1];

                listdepartemen.value = listcalons.data.list_departemen
                listproses.value = listcalons.data.list_proses
                listagama.value = listcalons.data.list_agama
                listdepartemenv2.value = listsiswa.data.list_dp
                listTahunAjaran.value = listsiswa.data.list_tahunajaran
                listagamav2.value = listsiswa.data.list_ag
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
        listagamav2.value
    })

    return {
        listagama,
        listdepartemenv2,
        listproses,
        listTahunAjaran,
        listdepartemen,
        listagamav2,
        filterData,
        filters,
        getList
        // dataset,
        // header,
        // datatable,
        // sum,
        // getListFilterCalons,
        // getListFilterSiswa
        // filters
    }
}
