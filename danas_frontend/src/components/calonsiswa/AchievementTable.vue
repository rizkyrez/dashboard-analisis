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
          <td>104</td>
          <td>87</td>
          <td>191</td>
          <td>83,65%</td>
      </tr>
    </tbody>
  </table>

</template>

<script>
/* eslint-disable */
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import Level from '@/components/Level'

export default {
  name: 'AchievementTable',
  components: {
    Level,
  },
  props: {
    checkable: Boolean
  },
  setup () {
    const store = useStore()

    const darkMode = computed(() => store.state.darkMode)

    return {
      darkMode,
    }
  }
}
</script>
