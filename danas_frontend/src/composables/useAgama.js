import { ref, watchEffect, toRefs, watch, onBeforeMount} from 'vue'
import axios from 'axios'

export default function useAgama(){
    const dataset = ref([])

    const getAgamaCalons = () => {
        axios
            .get('/api/v1/calons/agama/')
            .then(res => {
                dataset.value = res.data.agdata
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error.res.data))
                console.log(JSON.stringify(error))
            })
    }

    const getAgamaSiswa = async () => {
        await axios
            .get('/api/v1/siswa/agama/')
            .then(res => {
                dataset.value = res.data.dataset
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error))
                console.log(JSON.stringify(error.response.data))
            })
    }

    const legendat = computed(() => {
        let arobj = agdata.value

        let prop = arobj.map(p => p.name)

        return prop
    })

    onBeforeMount(getAgamaSiswa)

    watchEffect(() => {
        dataset.value
        legendat
    });

    return {
        getAgamaCalons,
        getAgamaSiswa,
        dataset
    }
};
