<template>
  <table class="border border-collapse border-green-400 text-xs">
    <thead>
    <tr class="bg-green-300">
      <th class="font-semibold w-1">No</th>
      <th class="font-semibold">Area</th>
      <th class="font-semibold">Gelombang TA 2021/2022</th>
      <th class="font-semibold">Gelombang TA 2022/2023</th>
      <th class="font-semibold">Grand Total</th>
    </tr>
    </thead>
    <tbody>
      <tr >
          <td>1</td>
          <td data-label="area">
            blank
          </td>
          <td data-label="g21">21</td>
          <td data-label="g22">11</td>
          <td data-label="grdtotal1">32</td>
      </tr>
      <tr >
          <td>2</td>
          <td data-label="area">
            Green Lake City
          </td>
          <td data-label="g21">6</td>
          <td data-label="g22">14</td>
           <td data-label="grdtotal1">20</td>
      </tr>
      <tr>
          <td>3</td>
          <td data-label="area">
            Kosambi Baru
          </td>
          <td data-label="g21">4</td>
          <td data-label="g22">12</td>
          <td data-label="grdtotal1">16</td>
      </tr>

      <tr >
          <td>4</td>
          <td data-label="area">
            Taman Semanan Indah
          </td>
          <td data-label="g21">2</td>
          <td data-label="g22">7</td>
          <td data-label="grdtotal1">9</td>
      </tr>

      <tr >
          <td>5</td>
          <td data-label="area">
            Metro Permata
          </td>
          <td data-label="g21">1</td>
          <td data-label="g22">3</td>
          <td data-label="grdtotal1">4</td>
      </tr>

      <tr >
          <td>6</td>
          <td data-label="area">
            Metland
          </td>
          <td data-label="g21">0</td>
          <td data-label="g22">2</td>
          <td data-label="grdtotal1">2</td>
      </tr>

      <tr >
          <td>7</td>
          <td data-label="area">
            Kresek Indah
          </td>
          <td data-label="g21">1</td>
          <td data-label="g22">1</td>
          <td data-label="grdtotal1">2</td>
      </tr>

      <tr >
          <td>8</td>
          <td data-label="area">
            Green Village
          </td>
          <td data-label="g21">0</td>
          <td data-label="g22">1</td>
          <td data-label="grdtotal1">1</td>
      </tr>

      <tr >
          <td>9</td>
          <td data-label="area">
            Citra Garden
          </td>
          <td data-label="g21">0</td>
          <td data-label="g22">1</td>
          <td data-label="grdtotal1">1</td>
      </tr>

      <tr >
          <td>10</td>
          <td data-label="area">
            Green Palm
          </td>
          <td data-label="g21">0</td>
          <td data-label="g22">1</td>
          <td data-label="grdtotal1">1</td>
      </tr>

      <tr >
          <td>11</td>
          <td data-label="area">
            Poris Paradise
          </td>
          <td data-label="g21">1</td>
          <td data-label="g22">0</td>
          <td data-label="grdtotal1">1</td>
      </tr>

      <tr >
          <td data-label="area" colspan="2">
            Grand Total
          </td>
          <td data-label="g21">36</td>
          <td data-label="g22">53</td>
          <td data-label="grdtotal1">89</td>
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
  name: 'AreaTable',
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
