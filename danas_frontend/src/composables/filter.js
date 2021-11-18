import {ref, toRefs, reactive, onMounted, onBeforeMount, watch, watchEffect, onUpdated, onRenderTriggered, onBeforeUpdate} from 'vue'
import axios from 'axios'

const listdep = ref([])
const listproses = ref([])
const listagama = ref([])
const listwilayah = ref([])

const getlistdata = async () => {
    await axios
        .post('/api/v1/calons/filter')
        .then(res => {
            listdep.value = res.data.list_departemen
            listproses.value = res.data.list_proses
            listagama.value = res.data.list_agama
            listwilayah.value = res.data.list_wilayah
        })
        .catch(error => {

        })
}


const changefilter = async ($dep,$proses,$field3,$field4) => {
    const passdata = {
        departemen: $dep,

    }


    await axios
        .put('api/v1/calons/filter')
        .then(res => {

        })
}
