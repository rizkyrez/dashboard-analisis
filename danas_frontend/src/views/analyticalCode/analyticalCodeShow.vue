<template>
  <hero-bar>Forms</hero-bar>
  <main-section>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="border-t border-gray-200">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-base font-medium text-gray-500">
                ID
            </dt>
              {{ form.id}}
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-base font-medium text-gray-500">
              Name
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ form.name}}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-base font-medium text-gray-500">
              Description
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ form.description}}
            </dd>
          </div>
        </dl>
      </div>
    </div>
    <divider />
    <card-component title="Analytical Code Field" :icon="mdiBallot">
      <ag-grid-vue style="width: 100%; height: 70vh; border-radius:5px"
            class="ag-theme-alpine"
            :defaultColDef="defaultColDef"
            :columnDefs="columnDefs"
            :pagination="true"
            @cell-value-changed="onCellValueChanged"
            :rowData="rowData.value">
        </ag-grid-vue>
    </card-component>
  </main-section>
</template>
<script>
// /* eslint-disable */
import { ref, reactive, onMounted } from 'vue'
import MainSection from '@/components/MainSection'
import CardComponent from '@/components/CardComponent'
import HeroBar from '@/components/HeroBar'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import Divider from '@/components/Divider'
import { mdiBallot } from '@mdi/js'
import { AgGridVue } from 'ag-grid-vue3'
export default {
    name: 'Show',
    components: {
      HeroBar,
      MainSection,
      Divider,
      CardComponent,
      AgGridVue
    },
    props: {
        propClass: {
            type: Object,
            default: () => ({ empty: true })
        }
    },
    data: function () {
        return {
        }
    },
    setup (props, { emit }) {
        const titleStack = ref(['Admin', 'Forms'])
        const route = useRoute()
        const router = useRouter()

        const form = reactive({
          id: '',
          name: '',
          description: ''
        })

        const id = route.params.id

        const loadData = async () => {
          if (id) {
              await axios
              .get('/api/v1/analytical-code/' + id)
              .then(response => {
                  if (response.data.success) {
                      console.log(response.data.data)
                      Object.assign(form, response.data.data)
                  }
                  // console.log(errors.value)
              })
              .catch(error => {
                  if (error.response) {
                      console.log(error.response.data)
                      console.log(JSON.stringify(error.request))
                  } else if (error.message) {
                      console.log(error.message)
                      console.log(JSON.stringify(error.message))
                  }
              })
          }
        }

        const columnDefs = [
            { headerName: '#', valueGetter: 'node.rowIndex+1' },
            { field: 'name', headerName: 'Name', sortable: true, filter: true, editable: true },
            { field: 'data_type', headerName: 'DataType', sortable: true, filter: true, editable: true }
        ]

        const defaultColDef = {
            resizable: true,
            flex: 1
        }

        const rowData = reactive([])

        const loadDataField = async () => {
          await axios
          .get('/api/v1/analytical-code-field')
          .then(response => {
              if (response.data.success) {
                  rowData.value = response.data.data
              }
          })
          .catch(error => {
              if (error.response) {
                  console.log(error.response.data)
                  console.log(JSON.stringify(error.request))
              } else if (error.message) {
                  console.log(error.message)
                  console.log(JSON.stringify(error.message))
              }
          })
        }

        const onCellValueChanged = async (event) => {
          console.log('data after changes is: ', event.data)
          await axios
          .put('/api/v1/analytical-code-field/' + event.data.id, event.data)
          .then(response => {
              if (response.data.success) {
                  console.log(response.data)
              }
          })
          .catch(error => {
              if (error.response) {
                  console.log(error.response.data)
                  console.log(JSON.stringify(error.request))
              } else if (error.message) {
                  console.log(error.message)
                  console.log(JSON.stringify(error.message))
              }
          })
        }

        onMounted(() => {
            // get API from Laravel Backend
            loadData()
            loadDataField()
        })
        return {
            titleStack,
            form,
            route,
            router,
            mdiBallot,
            columnDefs,
            defaultColDef,
            rowData,
            onCellValueChanged
        }
    },
    methods: {
      onGridReady (params) {
        this.gridApi = params.api
        this.gridColumnApi = params.columnApi

        params.api.sizeColumnsToFit()
      }
    }
}
</script>
