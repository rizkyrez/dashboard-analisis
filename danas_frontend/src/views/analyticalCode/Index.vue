<template>
  <title-bar :title-stack="titleStack" />
  <hero-bar>Tables</hero-bar>
  <main-section>
    <notification color="info" :icon="mdiMonitorCellphone">
      <b>Responsive table.</b> Collapses on mobile
    </notification>

    <card-component class="mb-6" title="Clients" :icon="mdiAccountMultiple" has-table>
        <ag-grid-vue style="width: 100%; height: 70vh; border-radius:5px"
            class="ag-theme-alpine"
            :defaultColDef="defaultColDef"
            :columnDefs="columnDefs"
            :pagination="true"
            :context="context"
            :rowData="rowData.value">
        </ag-grid-vue>
    </card-component>
  </main-section>

  <bottom-other-pages-section />
</template>

<script>
/* eslint-disable */
import { ref, reactive, onMounted } from 'vue'
import { mdiMonitorCellphone, mdiAccountMultiple, mdiTableBorder, mdiTableOff } from '@mdi/js'
import MainSection from '@/components/MainSection'
import Notification from '@/components/Notification'
import CardComponent from '@/components/CardComponent'
import TitleBar from '@/components/TitleBar'
import HeroBar from '@/components/HeroBar'
import BottomOtherPagesSection from '@/components/BottomOtherPagesSection'
// import TitleSubBar from '@/components/TitleSubBar'
import { AgGridVue } from 'ag-grid-vue3'
import ButtonActionRender from '@/components/ButtonActionRender'
import axios from 'axios'

export default {
  name: 'Tables',
  components: {
    // TitleSubBar,
    MainSection,
    HeroBar,
    TitleBar,
    CardComponent,
    Notification,
    BottomOtherPagesSection,
    AgGridVue,
    ButtonActionRender
  },
  setup () {
    const titleStack = ref(['Admin', 'Tables'])
    const columnDefs = [
            { headerName: '#', valueGetter: 'node.rowIndex+1' },
            { field: 'name', headerName: 'Name', sortable: true, filter: true },
            { field: 'description', headerName: 'Description', sortable: true, filter: true },
            { field: 'id', headerName: 'Action', sortable: false, filter: false, editable:false, cellRendererFramework: 'ButtonActionRender' }
        ]

    const defaultColDef = {
            resizable: true
        }

    const rowData = reactive([])

    const refresh = async () => {
            // console.log(Vue.config) // Vue.config.globalProperties.$Progress.start()
            await axios
            .get('/api/v1/analytical-code')
            .then(response => {
                // assign state kelass with response data
                rowData.value = response.data.data
            }).catch(error => {
                console.log(error.response.data)
            })
        }
    onMounted(() => {
        // get API from Laravel Backend
        refresh()
    })
    return {
        titleStack,
        mdiMonitorCellphone,
        mdiAccountMultiple,
        mdiTableBorder,
        mdiTableOff,
        columnDefs,
        defaultColDef,
        rowData,
        refresh,
        ButtonActionRender
    }
  }
}
</script>
