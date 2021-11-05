<template>

  <table class="border border-collapse border-green-400 text-xs">
    <thead>
    <tr class="bg-green-300">
      <th class="font-semibold w-1">No</th>
      <th class="font-semibold">Agama</th>
      <th class="font-semibold">Jumlah</th>
    </tr>
    </thead>
    <tbody>
      <tr >
          <td>1</td>
          <td data-label="agama">
            Buddha
          </td>
          <td data-label="total">41</td>
      </tr>
      <tr>
          <td>2</td>
          <td data-label="agama">
              Islam
          </td>
          <td data-label="total">1</td>
      </tr>
      <tr>
          <td>3</td>
          <td data-label="agama">
            Katolik
          </td>
          <td data-label="total">9</td>
      </tr>
      <tr >
          <td>4</td>
          <td data-label="agama">
            Kong Hu Cu
          </td>
          <td data-label="total">9</td>
      </tr>
      <tr >
          <td>5</td>
          <td data-label="agama">
            Kristen
          </td>
          <td data-label="total">3</td>
      </tr>
      <tr >
          <td>6</td>
          <td data-label="agama">
            Null
          </td>
          <td data-label="total">10</td>
      </tr>
      <tr >
          <td data-label="agama" colspan="2">
            Grand Total
          </td>
          <td data-label="total">65</td>
      </tr>
    <!-- <tr v-for="client in itemsPaginated" :key="client.id">
      <checkbox-cell v-if="checkable" @checked="checked($event, client)"/>
      <td class="image-cell">
        <user-avatar :username="client.name" class="image" />
      </td>
      <td data-label="Name">{{ client.name }}</td>
      <td data-label="Company">{{ client.company }}</td>
      <td data-label="City">{{ client.city }}</td>
      <td data-label="Progress" class="progress-cell">
        <progress max="100" :value="client.progress">{{ client.progress }}</progress>
      </td>
      <td data-label="Created">
        <small class="text-gray-500 dark:text-gray-400" :title="client.created">{{ client.created }}</small>
      </td>
      <td class="actions-cell">
        <jb-buttons type="justify-start lg:justify-end" no-wrap>
          <jb-button class="mr-3" color="success" :icon="mdiEye" small @click="isModalActive = true" />
          <jb-button color="danger" :icon="mdiTrashCan" small @click="isModalDangerActive = true" />
        </jb-buttons>
      </td>
    </tr> -->
    </tbody>
  </table>

</template>

<script>
/* eslint-disable */
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import { mdiEye, mdiTrashCan } from '@mdi/js'
import ModalBox from '@/components/ModalBox'
import CheckboxCell from '@/components/CheckboxCell'
import Level from '@/components/Level'
import JbButtons from '@/components/JbButtons'
import JbButton from '@/components/JbButton'
import UserAvatar from '@/components/UserAvatar'

export default {
  name: 'AgamaTable',
  components: {
    ModalBox,
    CheckboxCell,
    Level,
    JbButtons,
    JbButton,
    UserAvatar
  },
  props: {
    checkable: Boolean
  },
  setup () {
    const store = useStore()

    const darkMode = computed(() => store.state.darkMode)

    const items = computed(() => store.state.clients)

    const isModalActive = ref(false)

    const isModalDangerActive = ref(false)

    const perPage = ref(10)

    const currentPage = ref(0)

    const checkedRows = ref([])

    const itemsPaginated = computed(
      () => items.value.slice(perPage.value * currentPage.value, perPage.value * (currentPage.value + 1))
    )

    const numPages = computed(() => Math.ceil(items.value.length / perPage.value))

    const currentPageHuman = computed(() => currentPage.value + 1)

    const pagesList = computed(() => {
      const pagesList = []

      for (let i = 0; i < numPages.value; i++) {
        pagesList.push(i)
      }

      return pagesList
    })

    const remove = (arr, cb) => {
      const newArr = []

      arr.forEach(item => {
        if (!cb(item)) {
          newArr.push(item)
        }
      })

      return newArr
    }

    const checked = (isChecked, client) => {
      if (isChecked) {
        checkedRows.value.push(client)
      } else {
        checkedRows.value = remove(checkedRows.value, row => row.id === client.id)
      }
    }

    return {
      darkMode,
      isModalActive,
      isModalDangerActive,
      currentPage,
      currentPageHuman,
      numPages,
      checkedRows,
      itemsPaginated,
      pagesList,
      checked,
      mdiEye,
      mdiTrashCan
    }
  }
}
</script>
