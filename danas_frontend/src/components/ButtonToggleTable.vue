<template>
  <component :is="is" :class="componentClass" :href="href" :type="computedType" :to="to" :target="target">
    <icon :path="mdiTableArrowLeft" size="24"/>
    <span :class="labelClass"> Table </span>
  </component>
</template>

<script>
import { mdiMenuUp, mdiArrowLeftThick, mdiTableArrowLeft } from '@mdi/js'
import { computed } from 'vue'
import { colorsButtons, colorsButtonsOutline } from '@/colors.js'
import Icon from '@/components/Icon'

export default {
  name: 'ButtonToggleTable',
  components: {
    Icon
  },
  props: {
    label: [String, Number],
    icon: String,
    href: String,
    target: String,
    to: [String, Object],
    type: String,
    color: {
      type: String,
      default: 'white'
    },
    small: Boolean,
    outline: Boolean,
    active: Boolean,
    as: String
  },
  setup (props) {
    const is = computed(() => {
      if (props.as) {
        return props.as
      }

      if (props.to) {
        return 'router-link'
      }

      if (props.href) {
        return 'a'
      }

      return 'button'
    })

    const computedType = computed(() => {
      if (is.value === 'button') {
        return props.type ?? 'button'
      }

      return null
    })

    const iconOnly = computed(() => props.icon && !props.label)

    const labelClass = computed(() => props.small && props.icon ? 'pr-1' : 'pr-2')

    const componentClass = computed(() => {
      const base = [
        'absolute',
        'inline-flex',
        'cursor-pointer',
        'justify-center',
        'items-center',
        'whitespace-nowrap',
        'focus:outline-none',
        'transition-colors',
        'focus:ring',
        'duration-150',
        'z-10',
        '-ml-4',
        'p-1',
        'sm:-ml-6',
        'border',
        'rounded',
        'right-0',
        'top-64',
        props.active ? 'ring ring-black dark:ring-white' : 'ring-blue-700',
        props.small ? 'p-0' : 'p-1',
        props.outline ? colorsButtonsOutline[props.color] : colorsButtons[props.color]
      ]

      return base
    })

    return {
      is,
      iconOnly,
      labelClass,
      computedType,
      componentClass,
      mdiMenuUp,
      mdiArrowLeftThick,
      mdiTableArrowLeft
    }
  }
}
</script>
