<template>

  <table class="border border-collapse border-green-400 text-xs">
    <thead>
    <tr class="bg-green-300">
      <th class="font-semibold">Gelombang TA 2021/2022</th>
      <th class="font-semibold">Gelombang TA 2022/2023</th>
      <th class="font-semibold">Grand Total</th>
      <th class="font-semibold">Achievement %</th>
    </tr>
    </thead>
    <tbody>
      <tr >
          <td data-label="ga21">104</td>
          <td data-label="ga22">87</td>
          <td data-label="gtotal">191</td>
          <td data-label="achievement">83,65%</td>
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
  name: 'AchievementTable',
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
