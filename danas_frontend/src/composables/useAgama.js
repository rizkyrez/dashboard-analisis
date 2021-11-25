import { ref, watchEffect, toRefs, watch} from 'vue'
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

    const getAgamaSiswa = () => {
        axios
            .get('/api/v1/siswa/agama/')
            .then(res => {
                dataset.value = res.data.dataset
            })
            .catch(error => {
                alert(error.message)
                console.log(JSON.stringify(error))
                console.log(JSON.stringify(error.res.data))
            })
    }

    watchEffect(() => {
        dataset.value
    });

    return {
        getAgamaCalons,
        getAgamaSiswa,
        dataset
    }
  };
